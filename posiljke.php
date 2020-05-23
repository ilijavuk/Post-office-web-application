<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();

    session_start();
    $_SESSION['uloga'] = 3;
    $_SESSION['kor_id'] = 1;
    $_SESSION['blokiran'] = 1;
    $uloga = 3;
    $kor_id = 1;

    if($uloga != null && $uloga >= 1){
        $upit = "SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime FROM korisnik;";
        $rezultat1 = $baza -> SelectDB($upit);
        $upit = "SELECT id_primatelja, spremnaZaIsporuku, cijenaPoKg, masa, t2.ime_primatelja, t3.naziv AS trenutni_ured, t1.id_trenutniUred, t1.id_konacniUred FROM posiljka AS t1 LEFT JOIN ( SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime_primatelja FROM korisnik ) AS t2 ON t1.id_primatelja = t2.korisnik_id LEFT JOIN postanskiured AS t3 ON t1.id_trenutniUred=t3.postanskiUred_id WHERE t1.id_posiljatelja =".$kor_id."";
        $rezultat2 = $baza -> SelectDB($upit);
        $upit = "SELECT posiljka_id, spremnaZaIsporuku, cijenaPoKg, masa, t2.ime_posiljatelja, t3.naziv AS trenutni_ured, t4.racun_id FROM posiljka AS t1 LEFT JOIN ( SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime_posiljatelja FROM korisnik ) AS t2 ON t1.id_posiljatelja = t2.korisnik_id LEFT JOIN postanskiured AS t3 ON t1.id_trenutniUred=t3.postanskiUred_id LEFT JOIN racun AS t4 ON t1.posiljka_id=t4.id_posiljka WHERE t1.id_primatelja='".$kor_id."'";
        $rezultat3 = $baza -> SelectDB($upit);

        if($uloga >= 2){
            $upit = "SELECT t1.naziv, COUNT(t3.posiljka_id) as broj_posiljki, SUM(case t4.placen when '1' then 1 else 0 end) AS broj_placenih FROM drzava AS t1 LEFT JOIN postanskiured AS t2 ON t1.drzava_id=t2.id_drzave LEFT JOIN posiljka AS t3 ON t2.postanskiUred_id=t3.id_pocetniUred LEFT JOIN racun AS t4 ON t3.posiljka_id=t4.id_posiljka GROUP BY t1.naziv";
            $rezultat4 = $baza -> SelectDB($upit);
        }
    }
    

    $baza -> zatvoriDB();
?>
<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Početna stranica</title>
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

        <link rel="stylesheet" href="css/ivuk.css">
    </head>
    <body>
        <header class="header">
            <span id="navButton" class="navButton">≡</span>
            <figure class="headerFigure"><a href="index.html"><img src="multimedija/post-icon.png" class="headerImage"></a></figure>
            <span class="headerText">POŠTE</span>
            <button onclick="location.href='./register.html'" class="button" style="grid-column: 4 / span 1;">Register</button>
            <button onclick="location.href='./login.html'" class="button" style="grid-column: 6 / span 1;">Login</button>
        </header>

        <nav class="navBar">
            <a href="posiljke.php" class="navLink active">Pošiljke</a>
            <a href="racuni.php" class="navLink">Računi</a>
            <a href="uredi.php" class="navLink">Uredi</a>
            <a href="drzave.php" class="navLink">Države</a>
            <a href="o_autoru.html" class="navLink">O autoru</a>
            <a href="dokumentacija.html" class="navLink">Dokumentacija</a>
            <a href="register.html" class="navLink mobileOnly">Register</a>
            <a href="login.html" class="navLink mobileOnly">Login</a>
        </nav>

        <div class="footerWrapper">
		<main>
            <div id="wrapper" class="rotateIn">
                <h1 class="heading">Pošiljke</h1>
                <h2 id="greska" style="color:red;"><?php echo isset($_SESSION['blokiran']) ? "Trenutno ste blokirani, pokušajte kasnije" : null ?></h2>
                <?php
                    if($uloga == null || $uloga == 0){
                        echo '<a class="linkWithUnderline" href="login.html">Prijavite se kako bi vidjeli sadržaj</a>';
                    }
                ?>
                <?php
                    if($uloga != null && $uloga >= 1){
                        echo '
                        <div class="switchShowingWrapper posiljkeSwitch">
                        <div id="showingLeft" class="switchShowing activeShow">Nova pošiljka</div>
                            <div id="showingMiddle1" class="switchShowing">Šaljem/Primam</div>';
                            if($uloga >= 2){
                            echo '
                            <div id="showingMiddle2" class="switchShowing">Statistika</div>';
                                if($uloga == 3){
                                    echo '<div id="showingRight" class="switchShowing">Zahtjevi</div>';
                                }
                            }
                        echo '</div>';
                    }
                ?>
               
                <?php 
                    if($uloga != null && $uloga >= 1){
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
                                    <input id="posaljiPosiljkuBtn" type = "submit" value = "Pošalji pošiljku" class="submit"><br>
                                    </div>';
                                
                                }
                                echo '</div>';

                        //prikaz šaljem/primam pošiljki
                        
                        //šaljem
                        echo '<div id="saljemPrimamWrapper" style="display:none;">
                                <h2>Pošiljke koje šaljem</h2>
                                <hr>
                                <table>
                                <thead>
                                    <th>Ime primatelja</th>
                                    <th>Trenutni ured</th>
                                    <th>Stigla na odredište?</th>
                                    <th>Cijena po kg</th>
                                    <th>Masa</th>
                                    <th>Spremna za isporuku</th>
                                </thead>
                            <body>';

                            while($red = mysqli_fetch_assoc($rezultat2)){
                                $stigla = ($red['id_trenutniUred'] == $red['id_konacniUred']) ? 'Da' : 'Ne';
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
                            </table>';

                        //primam
                        echo '
                            <h2 style="margin-top: 15px;">Pošiljke koje primam</h2>
                            <hr>
                            <table>
                                <thead>
                                    <th>Ime pošiljatelja</th>
                                    <th>Trenutni ured</th>
                                    <th>Cijena po kg</th>
                                    <th>Masa</th>
                                    <th>Spremna za isporuku</th>
                                </thead>
                            <body>';

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
                            </table>
                        </div>';
                    }
                ?>
                <?php
                    if($uloga != null && $uloga >= 2){
                        echo '<div id="statistikaWrapper" style="display:none;">
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
                        <table>
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
                        </table>
                        </div>';
                    }
                ?>
            </div>
        </main>  

        <footer class="footer">
            <span class="footerText">2020, Vuk Ilija</span>
        </footer>  
        </div><div id="overlay">
        </div>

        <div class="modal" style="top: 50%; transform: translateY(-50%);"> 
            <div class = "textbox" style="display:none;">
                <label for="id_posiljka">ID Pošiljke</label>
                <input type = "text" name = "id_posiljka" id="id_posiljka" class="text" disabled><br>
            </div>
            <div class = "textbox">
                <label for="ime_posiljatelja">Ime pošiljatelja</label>
                <input type = "text" name = "ime_posiljatelja" id="ime_posiljatelja" class="text" disabled><br>
            </div>
            <div class = "textbox">
                <label for="cijenaPoKg">Cijena po kg</label>
                <input type = "text" name = "cijenaPoKg" id="cijenaPoKg" class="text"  disabled><br>
            </div>
            <div class = "textbox">
                <label for="masa">Masa</label>
                <input type = "text" name = "masa" id="masa" class="text" disabled><br>
            </div>
            <div class="buttonWrapper" style="width: 100%;">
                <input id="zatražiRačunBtn" type = "submit" value = "Zatraži račun" class="button add" style="margin: 0 auto; width: 150px;"><br>
            </div>
        </div>
    </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="javascript/ivuk.js"></script>
</html>