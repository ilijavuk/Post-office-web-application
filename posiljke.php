<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();

    session_start();
    if(!isset($_SESSION['uloga'])){
        $_SESSION['uloga'] = 0;
    }
    
    
    if($_SESSION['uloga']  >= 1){
        $upit = "SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime FROM korisnik;";
        $rezultat1 = $baza -> SelectDB($upit);
        $upit = "SELECT id_primatelja, spremnaZaIsporuku, cijenaPoKg, masa, t2.ime_primatelja, t3.naziv AS trenutni_ured, t1.id_trenutniUred, t1.id_konacniUred FROM posiljka AS t1 LEFT JOIN ( SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime_primatelja FROM korisnik ) AS t2 ON t1.id_primatelja = t2.korisnik_id LEFT JOIN postanskiured AS t3 ON t1.id_trenutniUred=t3.postanskiUred_id WHERE t1.id_posiljatelja =".$_SESSION['kor_id']."";
        $rezultat2 = $baza -> SelectDB($upit);
        $upit = "SELECT posiljka_id, spremnaZaIsporuku, cijenaPoKg, masa, t2.ime_posiljatelja, t3.naziv AS trenutni_ured, t4.racun_id FROM posiljka AS t1 LEFT JOIN ( SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime_posiljatelja FROM korisnik ) AS t2 ON t1.id_posiljatelja = t2.korisnik_id LEFT JOIN postanskiured AS t3 ON t1.id_trenutniUred=t3.postanskiUred_id LEFT JOIN racun AS t4 ON t1.posiljka_id=t4.id_posiljka WHERE t1.id_primatelja='".$_SESSION['kor_id']."'";
        $rezultat3 = $baza -> SelectDB($upit);

        if($_SESSION['uloga']  >= 2){
            $upit = "SELECT t1.naziv, COUNT(t3.posiljka_id) as broj_posiljki, SUM(case t4.placen when '1' then 1 else 0 end) AS broj_placenih FROM drzava AS t1 LEFT JOIN postanskiured AS t2 ON t1.drzava_id=t2.id_drzave LEFT JOIN posiljka AS t3 ON t2.postanskiUred_id=t3.id_pocetniUred LEFT JOIN racun AS t4 ON t3.posiljka_id=t4.id_posiljka WHERE t2.id_moderatora=".$_SESSION['kor_id']." GROUP BY t1.naziv";
            $rezultat4 = $baza -> SelectDB($upit);
            $upit = "SELECT posiljka_id, t2.naziv AS trenutni_ured, t3.naziv AS konacni_ured FROM posiljka AS t1 LEFT JOIN postanskiUred AS t2 ON t1.id_trenutniUred=t2.postanskiUred_id LEFT JOIN postanskiured AS t3 ON t1.id_konacniUred=t3.postanskiUred_id WHERE t2.id_moderatora = '".$_SESSION['kor_id']."' AND spremnaZaIsporuku='0';";
            $rezultat7 = $baza -> SelectDB($upit);
            $upit = "SELECT * FROM postanskiured;";
            $rezultat5 = $baza -> SelectDB($upit);
        }

        if($_SESSION['uloga']  == 3){
            $upit = "SELECT posiljka_id, ime_posiljatelja, ime_primatelja, id_pocetniUred, id_konacniUred, id_trenutniUred, masa FROM `posiljka` AS t1 LEFT JOIN (SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime_posiljatelja FROM korisnik) AS t2 ON t1.id_posiljatelja=t2.korisnik_id LEFT JOIN (SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime_primatelja FROM korisnik) AS t3 ON t1.id_primatelja=t3.korisnik_id WHERE cijenaPoKg IS NULL";
            $rezultat6 = $baza -> SelectDB($upit);
        }
    }
    $baza -> zatvoriDB();
?>
<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Pošiljke</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Ilija Vuk">
        <meta name="keywords" content="Project home page">
        <meta name="description" content="Home page for the WebDiP Project">
        <meta name='date' content='May. 19, 2020'>
        <meta name="referrer" content="origin-when-cross-origin"><link rel="icon" href="multimedija/favicon.png">
        <meta property="og:image" content="multimedija/favicon.png" />
        <meta property="og:image:secure_url" content="multimedija/favicon.png" /> 
        <meta property="og:type" content="Website for a project" /> 
        <meta property="og:title" content="WebDiP Project - Ilija Vuk" />

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css"/>
        <link rel="stylesheet" href="css/ivuk.css">
        <style type="text/css">
            @media print
            {
                body * { visibility: hidden; }
                #statistikaWrapper *{ visibility: visible; }
                #statistikaWrapper { position: absolute; top: 70px; left: 50%; transform: translateX(-50%) scale(1.5); }
                #canvas { background-color: #FFF;}
            }
        </style>
    </head>
    <body>
       <?php
            require('komponente/header.php');
            require('komponente/navBar.php');
       ?>

        <div class="footerWrapper">
		<main>
            <div id="wrapper" class="rotateIn">
                <h1 class="heading">Pošiljke</h1>
                <h2 id="greska" style="color:red;"><?php echo isset($_SESSION['blokiran']) ? "Trenutno ste blokirani, pokušajte kasnije" : null ?></h2>
                <?php
                    if($_SESSION['uloga']  == 0){
                        echo '<a class="linkWithUnderline" href="login.php">Prijavite se kako bi vidjeli sadržaj</a>';
                    }
                ?>
                <?php
                    if($_SESSION['uloga']  >= 1){
                        echo '
                        <div class="switchShowingWrapper">
                        <div id="showingLeft" class="switchShowing activeShow">Nova pošiljka</div>
                            <div id="showingMiddle1" class="switchShowing">Šaljem/Primam</div>';
                            if($_SESSION['uloga']  >= 2){
                            echo '
                            <div id="showingMiddle2" class="switchShowing">Statistika</div>';
                                if($_SESSION['uloga']  == 3){
                                    echo '<div id="showingRight" class="switchShowing">Zahtjevi</div>';
                                }
                            }
                        echo '</div>';
                    }
                ?>
               
                <?php 
                    if($_SESSION['uloga']  >= 1){
                        //nova pošiljka
                        echo '<div id="novaPosiljkaWrapper">
                              <div class = "textbox">
                                    <label for="ime_primatelja">Kome želite poslati pošiljku?</label>
                                    <select class="select-css" id="ime_primatelja" style="width: 100%;" '; 
                                    if(isset($_SESSION["blokiran"])){ 
                                        echo 'disabled';
                                    }
                                    echo ' >';
                                   
                                        while($red = mysqli_fetch_assoc($rezultat1)){
                                            echo '
                                                <option value = '.$red["korisnik_id"].'> 
                                                    '.$red["ime"].'
                                                </option>
                                            ';
                                        }
                                    echo '
                                </select> 
                                </div>
                                <div class = "textbox">
                                    <label for="masa">Masa</label>
                                    <input type = "number" name = "masa" id="masa" class="text"  style="border: 1px solid #707070;" '; 
                                    if(isset($_SESSION["blokiran"])){ 
                                        echo 'disabled';
                                    }
                                    echo ' ><br>
                                </div>';
                                if(!isset($_SESSION["blokiran"])){ 
                                    echo '<div class="buttonWrapper">
                                    <input id="posaljiPosiljkuBtn" type = "submit" value = "Pošalji pošiljku" class="button add"><br>
                                    </div>';
                                
                                }
                                echo '</div>';

                        //prikaz šaljem/primam pošiljki
                        
                        //šaljem
                        echo '<div id="saljemPrimamWrapper" style="display:none;">
                                <h2>Pošiljke koje šaljem</h2>
                                <hr>
                                <div class="tableWrapper">
                                <table id="saljemTable">
                                <thead>
                                    <th>Primatelj</th>
                                    <th>Trenutno</th>
                                    <th>Stigla?</th>
                                    <th>kn/kg</th>
                                    <th>Masa</th>
                                    <th>Spremna</th>
                                </thead>
                            <tbody>';
                            while($red = mysqli_fetch_assoc($rezultat2)){
                                $stigla = ($red['id_trenutniUred'] == $red['id_konacniUred'] && $red['id_trenutniUred'] != null) ? 'Da' : 'Ne';
                                echo  ' <tr>
                                <td>'.$red['ime_primatelja'].'</td>
                                <td>'.$red['trenutni_ured'].'</td>
                                <td>'.$stigla.'</td>
                                <td>'.$red['cijenaPoKg'].'</td>
                                <td>'.$red['masa'].'</td>
                                <td>'.$red['spremnaZaIsporuku'].'</td>
                            </tr>';
                            }

                            echo '</tbody>
                            </table></div>';

                        //primam
                        echo '
                            <h2 style="margin-top: 15px;">Pošiljke koje primam</h2>
                            <hr>
                            <div class="tableWrapper">
                            <table id="primamTable">
                                <thead>
                                    <th>Ime pošiljatelja</th>
                                    <th>Trenutni ured</th>
                                    <th>Cijena po kg</th>
                                    <th>Masa</th>
                                    <th>Spremna za isporuku</th>
                                    <th style="display:none;">ID</th>
                                </thead>
                            <tbody>';

                            while($red = mysqli_fetch_assoc($rezultat3)){
                                if($red['spremnaZaIsporuku'] == 1 && $red['racun_id'] == null){
                                    echo  ' <tr style="outline: 5px solid green; cursor: pointer;" class="spremanZaIsporuku">';
                                }
                                else{
                                    echo  ' <tr>';
                                }
                                echo'
                                    <td>'.$red['ime_posiljatelja'].'</td>
                                    <td>'.$red['trenutni_ured'].'</td>
                                    <td>'.$red['cijenaPoKg'].'</td>
                                    <td>'.$red['masa'].'</td>
                                    <td>'.$red['spremnaZaIsporuku'].'</td>
                                    <td style="display:none;">'.$red['posiljka_id'].'</td>
                                </tr>';
                            }

                            echo '</tbody>
                            </table></div>';

                        if($_SESSION['uloga']  >= 2){
                                

                        //primam(moderator)
                        echo '
                        <h2 style="margin-top: 15px;">Primljene pošiljke(Moderator)</h2>
                        <hr>
                        <div class="tableWrapper">
                        <table id="primamModerator">
                            <thead>
                                <th style="display: none;">ID</th>
                                <th>Trenutni ured</th>
                                <th>Konačni ured</th>
                                <th>Stigao na odredište</th>
                            </thead>
                        <tbody id="primamModeratorTBody">';

                        while($red = mysqli_fetch_assoc($rezultat7)){
                            $stigaoNaOdrediste = ($red['trenutni_ured'] == $red['konacni_ured']) ? 'Da' : 'Ne' ;
                            echo  '<tr class="'.$stigaoNaOdrediste.'">
                            <td style="display: none;"> '.$red['posiljka_id'].'</td>
                            <td>'.$red['trenutni_ured'].'</td>
                            <td>'.$red['konacni_ured'].'</td>
                            <td>'.$stigaoNaOdrediste.'</td>
                        </tr>';
                        }

                        echo '</tbody>
                        </table></div>';
                           
                        }

                        echo '</div>';
                    }
                ?>
                <?php
                    if($_SESSION['uloga']  >= 2){
                        echo '
                        <div id="statistikaWrapper" style="display:none;">
                            <h4 onclick="window.print();"> Print </h4>
                            <div class="switchShowingWrapper posiljkeSwitch"  style="border:none;">
                                <div class = "textbox">
                                    <label for="od">Od</label>
                                    <input type = "date" name = "od" id="od" class="text">
                                </div>
                                <div class = "textbox">
                                    <label for="do">Do</label>
                                    <input type = "date" name = "do" id="do" class="text">
                                </div>
                                <div class="buttonWrapper" style="width: 100%; align-self: center; justify-content: start; position: relative; top: 7px;">
                                    <input id="filtrirajBtn" type = "submit" value = "Filtriraj" class="button add" style="margin: 0 auto; width: 150px;"><br>
                                </div>
                            </div>
                            ';

                            echo '
                            <h2 style="margin-top: 15px;">Statistika</h2>
                            <hr>
                            <div class="tableWrapper">
                            <table id="statistikaTable">
                                <thead>
                                    <th>Naziv</th>
                                    <th>Broj poslanih pošiljki</th>
                                    <th>Broj plaćenih pošiljki</th>
                                </thead>
                                <tbody id="statistikaTbody">';
                                while($red = mysqli_fetch_assoc($rezultat4)){
                                    $broj_posiljki = $red['broj_posiljki'] ==  '' ? 0 : $red['broj_posiljki'];
                                    echo  ' <tr>
                                        <td>'.$red['naziv'].'</td>
                                        <td>'.$broj_posiljki.'</td>
                                        <td>'.$red['broj_placenih'].'</td>
                                    </tr>';
                                }
                            echo '</tbody>
                            </table></div>';
                            if($rezultat4->num_rows > 0){
                                echo '<span id="print"> &#128438; </span>
                                <figure id="saveAsPDF"><img src="multimedija/pdf-icon.svg" alt="Pdf icon"></figure>
        
                                <div id="forPrint">
                                <h2 style="margin-top: 50px;">PP - Poslane pošiljke</h2>
                                <h2>BP - Broj plaćenih</h2>
                                <canvas id="canvas" height="300" style="border:1px solid #f1cd7b;">
                                </canvas>
                                </div>';
                            }
                        echo '
                        </div>
                        ';
                    }
                ?>
                <?php
                    if($_SESSION['uloga']  >= 3){
                        echo '<div id="zahtjeviZaPosiljkamaWrapper" style="display:none;"><h2>Zahtjevi za pošiljkama</h2>
                        <hr>
                        <div class="tableWrapper">
                        <table id="zahtjeviTable">
                        <thead>
                            <th style="display:none;">ID</th>
                            <th>Ime pošiljatelja</th>
                            <th>Ime primatelja</th>
                            <th>Masa</th>
                            <th>Cijena po Kg</th>
                            <th>Početni ured</th>
                            <th>Konačni ured</th>
                        </thead>
                    <tbody id="prihvacanjeZahtjeva">';
                   
                    while($red = mysqli_fetch_assoc($rezultat6)){
                        echo  ' <tr>
                        <td style="display:none;">'.$red['posiljka_id'].'</td>
                        <td>'.$red['ime_posiljatelja'].'</td>
                        <td>'.$red['ime_primatelja'].'</td>
                        <td>'.$red['masa'].'</td>
                        <td><input type="textbox" class="tableInput" id="cijenaPoKg" style="background: transparent;"></td>    
                        <td>
                            <select class="select-css" id="select" style="height: 29px; width:100%; background: transparent;">
                            <option value="-1" selected></option>
                            ';
                            $rezultat5->data_seek(0);
                            while($red = mysqli_fetch_assoc($rezultat5)){
                                echo '
                                    <option value = '.$red['postanskiUred_id'].'> 
                                        '.$red["naziv"].'
                                    </option>
                                ';
                            }
                            echo '
                            </select> 
                        </td>

                        <td>
                            <select class="select-css" id="select" style="height: 29px; width:100%; background: transparent;">
                                <option value="-1" selected></option>';
                                $rezultat5->data_seek(0);
                                while($red = mysqli_fetch_assoc($rezultat5)){
                                    echo '
                                        <option value = '.$red['postanskiUred_id'].'> 
                                            '.$red["naziv"].'
                                        </option>
                                    ';
                                }
                                echo '
                            </select> 
                        </td>                        
                    </tr>';
                    }

                    echo '</tbody>
                    </table> </div>
                    <div class="buttonWrapper">
                        <input id="azurirajPosiljkeBtn" type = "submit" value = "Ažuriraj pošiljke" class="button add"><br>
                    </div></div>';
                    }
                ?>
            </div>
        </main>  

        <?php
            require('komponente/podnozje.php');
        ?>
        </div><div id="overlay">
        </div>

        <div class="modal" style="top: 50%; transform: translateY(-50%);"> 
            <div id="zatraziRacunWrapper" style="display: none;">
                <div class = "textbox" style="display:none;">
                    <label for="id_posiljka">ID Pošiljke</label>
                    <input type = "text" name = "id_posiljka" id="id_posiljka" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="ime_posiljatelja">Ime pošiljatelja</label>
                    <input type = "text" name = "ime_posiljatelja" id="ime_posiljatelja" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="cijenaPoKgModal">Cijena po kg</label>
                    <input type = "text" name = "cijenaPoKgModal" id="cijenaPoKgModal" class="text"  disabled><br>
                </div>
                <div class = "textbox">
                    <label for="masaModal">Masa</label>
                    <input type = "text" name = "masaModal" id="masaModal" class="text" disabled><br>
                </div>
                <div class="buttonWrapper" style="width: 100%;">
                    <input id="zatražiRačunBtn" type = "submit" value = "Zatraži račun" class="button add" style="margin: 0 auto; width: 150px;"><br>
                </div>
            </div>
            <?php
            if($_SESSION['uloga'] >= 2){
            echo '
            <div id="proslijediPosiljkuWrapper">
                <div class = "textbox">
                    <label for="posiljka_id">ID Pošiljke</label>
                    <input type = "text" name = "posiljka_id" id="posiljka_id" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="konacni_ured">Konačni ured</label>
                    <input type = "text" name = "konacni_ured" id="konacni_ured" class="text" disabled><br>
                </div>
                <div class = "textbox" id="sljedeci_ured_txtBox">
                    <label for="sljedeci_ured">Sljedeći ured</label>
                    <select class="select-css" id="sljedeci_ured" style="width:100%;">
                        <option value="-1" selected></option>';
                        $rezultat5->data_seek(0);
                        while($red = mysqli_fetch_assoc($rezultat5)){
                            echo '
                                <option value = "'.$red['postanskiUred_id'].'"> 
                                    '.$red["naziv"].'
                                </option>
                            ';
                        }
                        echo '</select> 
                </div>
                <div class="buttonWrapper" style="width: 100%;">
                    <input id="proslijediPosiljkuBtn" type = "submit" value = "Proslijedi" class="button add" style="margin: 0 auto; width: 150px;"><br>
                </div>
            </div>';
            }
            ?>
        </div>
    </div>
    <div id="snackbar"></div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
    <script src="javascript/ivuk.js"></script>
    <script src="vanjske_biblioteke/jsPDF-1.3.2/dist/jspdf.min.js"></script>
</html>
