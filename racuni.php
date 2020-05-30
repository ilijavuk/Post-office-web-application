<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();
    session_start();
    if(!isset($_SESSION['uloga'])){
        $_SESSION['uloga'] = 0;
    }

    $rezultat2 = nulL;
    if($_SESSION['uloga'] >= 1){
        $upit = "SELECT * FROM racun AS t1 LEFT JOIN (SELECT id_posiljatelja, posiljka_id FROM posiljka) AS t2 ON t1.id_posiljka = t2.posiljka_id  WHERE t2.id_posiljatelja='".$_SESSION['kor_id']."';";
        $rezultat = $baza -> SelectDB($upit);  
        if($_SESSION['uloga']  >= 2){
            $upit = "SELECT * FROM racun AS t1 LEFT JOIN (SELECT id_posiljatelja, posiljka_id FROM posiljka) AS t2 ON t1.id_posiljka = t2.posiljka_id;";
            $rezultat2 = $baza -> SelectDB($upit);
            $upit = "SELECT * FROM racun WHERE id_moderatora=".$_SESSION['kor_id']." AND puniIznos IS NULL;";
            $rezultat3 = $baza -> SelectDB($upit);
        }
    }
    $baza -> zatvoriDB();
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Računi</title>
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
    </head>
    <body>
       <?php
            require('komponente/header.php');
            require('komponente/navBar.php');
       ?>
        
        <div class="footerWrapper">
		<main>
             <div id="wrapper">
                <h1 class="heading">Računi</h1>
                <?php
                    if($_SESSION['uloga']  == 0){
                        echo '<a class="linkWithUnderline" href="login.php">Prijavite se kako bi vidjeli sadržaj</a>';
                    }
                    if($_SESSION['uloga']  >= 2){
                        echo '
                        <div class="switchShowingWrapper">
                            <div id="showingLeft" class="switchShowing activeShow">Moji računi</div>
                            <div id="showingMiddle" class="switchShowing">Izdani</div>
                            <div id="showingRight" class="switchShowing">Zahtjevi</div>
                        </div>
                        ';
                    }
                ?>
                <?php
                    if($_SESSION['uloga']  >= 1){
                        echo '<table id="my">
                        <thead>
                            <th>Vrijeme izdavanja</th>
                            <th>Plaćen</th>
                            <th>Iznos pošiljke</th>
                            <th>Puni iznos</th>
                            <th>Slika</th>
                        </thead>
                        <tbody>';
                        while($red = mysqli_fetch_assoc($rezultat)){
                            if($red['placen'] == 0){        
                                echo '<tr style="outline: 3px solid red; cursor:pointer;" class="neplacen">';
                            }
                            else{
                                echo '<tr>';
                            }
                            echo   '<td>'.$red['vrijemeIzdavanja'].'</td>
                                    <td>'.$red['placen'].'</td>
                                    <td>'.$red['iznos'].' kn</td>
                                    <td>'.$red['puniIznos'].' kn</td>
                                    <td style="text-align:center;"><img src="'.$red['slika'].'"  height=50/></td>
                                    <td style="display:none;">'.$red['racun_id'].'</td>
                                </tr>';   
                        }
                        echo '
                        </tbody>
                    </table>';
                    }
                ?>
                <?php 
                    if($_SESSION['uloga']  >= 2){
                        echo '
                        <table id="izdani" style="display: none;">
                        <thead>
                            <th>Vrijeme izdavanja</th>
                            <th>Plaćen</th>
                            <th>Iznos pošiljke</th>
                            <th>Puni iznos</th>
                            <th>Slika</th>
                        </thead>
                        <tbody>';
                        while($red = mysqli_fetch_assoc($rezultat2)){
                                if($red['placen'] == 0){        
                                    echo '<tr style="outline: 3px solid red; cursor:pointer;" class="neplacenModerator">';
                                }
                                else{
                                    echo '<tr>';
                                }
                                echo   '<td>'.$red['vrijemeIzdavanja'].'</td>
                                    <td>'.$red['placen'].'</td>
                                    <td>'.$red['iznos'].' kn</td>
                                    <td>'.$red['puniIznos'].' kn</td>
                                    <td style="text-align:center;"><img src="'.$red['slika'].'"  height=50/></td>
                                    <td style="display:none;">'.$red['racun_id'].'</td>
                                    <td style="display:none;">'.$red['rokZaPlacanje'].'</td>
                                </tr>';   
                            }
                            echo '
                            </tbody>
                        </table>';
                    }
                ?>
                <?php 
                    if($_SESSION['uloga'] >= 2){
                    echo '
                        <div id="zahtjevi"  style="display: none;">
                            <table>
                            <thead>
                                <th>Vrijeme izdavanja</th>
                                <th>Iznos pošiljke</th>
                                <th>Iznos obrade</th>
                            </thead>
                            <tbody id="zahtjeviZaRacune">';
                            if($rezultat3->num_rows == 0){
                                echo '<tr><td colspan=3>Trenutno nemate zahtjeva</tr>';
                            }
                            while($red = mysqli_fetch_assoc($rezultat3)){
                                echo '<tr">
                                    <td>'.$red['vrijemeIzdavanja'].'</td>
                                    <td>'.$red['iznos'].' kn</td>
                                    <td> <input type="textbox" class="tableInput" style="background: transparent;"></td>
                                    <td style="display:none;" >'.$red['racun_id'].'</td>
                                </tr>';   
                                }
                                echo '
                                </tbody>
                            </table>';
                            if($rezultat3->num_rows > 0){
                                echo '<div class="buttonWrapper">
                                <input id="azurirajRacuneBtn" type = "submit" value = "Ažuriraj račune" class="button add"><br>
                            </div>';
                            }
                            echo'
                    </div>';
                    }
                ?>
        </main>  
        
        <?php
            require('komponente/podnozje.php');
        ?> 
        </div>
        <div id="overlay">
        </div>

        <div class="modal"> 
            <div class = "textbox" style="display:none;">
                <label for="racun_id">ID Računa</label>
                <input type = "text" name = "racun_id" id="racun_id" class="text" disabled><br>
            </div>
            <div id="updateRacun" style="display: none;">
                <div class = "textbox">
                    <label for="vrijemeIzdavanja">Vrijeme izdavanja</label>
                    <input type = "text" name = "vrijemeIzdavanja" id="vrijemeIzdavanja" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="rokPlacanja">Rok plaćanja</label>
                    <input type = "text" name = "rokPlacanja" id="rokPlacanja" class="text"  disabled><br>
                </div>
                <div class = "textbox">
                    <label for="placen">Plaćen</label>
                    <input type = "text" name = "placen" id="placen" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="iznos">Iznos pošiljke</label>
                    <input type = "text" name = "iznos" id="iznos" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="puniIznos">Puni iznos</label>
                    <input type = "text" name = "puniIznos" id="puniIznos" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="slika">Slika(URL)</label>
                    <input type = "text" name = "slika" id="slika" class="text"><br>
                </div>
                <div style="display: block; margin-bottom: 15px; height: 20px;">
                    <span style = "float:left; margin-right: 15px; font-size: 20px;"> Objavi sliku </span>
                    <div class="checkboxContainer" style="float:left;">
                        <input type="checkbox" class="checkbox" id="dopustenje"/>
                        <span class="checkmark"></span>
                    </div>
                </div>
                <div class="buttonWrapper" style="width: 100%;">
                    <input id="platiRacun" type = "submit" value = "Plati račun" class="button add" style="margin: 0 auto; width: 150px;"><br>
                </div>
            </div>
            <div id="blokirajKorisnika" style="display:none;">
                <div class = "textbox">
                    <label for="korisnik_id">ID korisnika</label>
                    <input type = "text" name = "korisnik_id" id="korisnik_id" class="text" disabled><br>
                </div>
                <div class = "textbox" style="display: none;">
                    <label for="rok">Rok</label>
                    <input type = "text" name = "rok" id="rok" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="ime_korisnika">Ime korisnika</label>
                    <input type = "text" name = "ime_korisnika" id="ime_korisnika" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="rok_za_placanje">Rok plaćanja</label>
                    <input type = "text" name = "rok_za_placanje" id="rok_za_placanje" class="text" disabled><br>
                </div>
                <div class = "textbox">
                    <label for="blokirajNa">Blokiraj korisnika na(broj sati)</label>
                    <input type = "number" name = "blokirajNa" id="blokirajNa" value="168" class="text"><br>
                </div> 
                <div id="buttonWrapperBlock" class="buttonWrapper" style="width: 100%; display:none">
                    <input id="blockBtn" type = "submit" value = "Blokiraj" class="button add" style="margin: 0 auto; width: 150px;"><br>
                </div>       
            </div>
        </div>
        <div id="snackbar"></div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
    <script src="javascript/ivuk.js"></script>
</html>