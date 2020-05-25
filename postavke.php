<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();
    session_start();
    if(!isset($_SESSION['uloga'])){
        $_SESSION['uloga'] = 0;
    }

    if($_SESSION['uloga'] == 3){
        $upit = "SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') as ime FROM korisnik WHERE id_status = 3 OR neuspjeliLogin >= 3;";
        $rezultat = $baza -> SelectDB($upit);
        $upit = "SELECT t1.radnja, t1.upit, t2.ime, t3.naziv FROM dnevnik AS t1 LEFT JOIN (SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime FROM korisnik) AS t2 ON t1.id_korisnik=t2.korisnik_id LEFT JOIN tip AS t3 ON t1.id_tip = t3.tip_id";
        $rezultat2 = $baza -> SelectDB($upit);
    }    
    $baza -> zatvoriDB();
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Postavke</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Ilija Vuk">
        <meta name="keywords" content="Project settings page">
        <meta name="description" content="Settings page for the WebDiP Project">
        <meta name='date' content='May. 19, 2020'>
        <meta name="referrer" content="origin-when-cross-origin"><link rel="icon" href="multimedija/favicon.png">
        <meta property="og:image" content="multimedija/favicon.png" />
        <meta property="og:image:secure_url" content="multimedija/favicon.png" /> 
        <meta property="og:type" content="Website for a project" /> 
        <meta property="og:title" content="WebDiP Project - Ilija Vuk" />

        <link rel="stylesheet" href="css/ivuk.css">
    </head>
    <body>
       <?php
            require('komponente/header.php');
            require('komponente/navBar.php');
       ?>

        <div class="footerWrapper">
		<main>
            <div id="wrapper" class="rotateIn">
                <h1 class="heading">Postavke</h1>
                <?php
                   
                    if($_SESSION['uloga']  == 3){
                        echo '
                        <div class="switchShowingWrapper">
                            <div id="showingLeft" class="switchShowing activeShow">Postavke</div>
                            <div id="showingRight" class="switchShowing">Admin</div>
                        </div>
                        ';
                            
                        echo 
                        '
                        <div id="adminOnly" style="display:none;">
                        <h2>Blokirani korisnici</h2>
                        <hr>
                        <table>
                            <thead>
                                <th>ID Korisnika</th>
                                <th>Ime</th>
                            </thead>
                            <tbody>';
                            while($red = mysqli_fetch_assoc($rezultat)){
                                
                                    echo '
                                    <tr>
                                        <td>'.$red['ime'].'</td>
                                        <td style="display:none;">'.$red['korisnik_id'].'</td>
                                        <td style="cursor: pointer;" class="odblokiraj">Odblokiraj</td>
                                    </tr>';   
                            }
                            echo '
                            </tbody>
                        </table>
                        
                        <h2 style="margin-top: 50px;">Uvjeti korištenja</h2>
                        <hr>
                        <div class="buttonWrapper">
                            <input id="resetirajUvjeteBtn" type = "submit" value = "Resetiraj uvjete korištenja" class="button add"><br>
                        </div>

                        <h2 style="margin-top: 50px;">Postavke</h2>
                        <hr>
                        <div class = "textbox inlineWithBtn" id="kor_imeTextBox">
                            <input type = "text" name = "trajanjeKolacica" id="trajanjeKolacica" class="text" style="border: 1px solid #707070;" placeholder="Trajanje kolačića"><br>
                        </div>
                        <div class="buttonWrapper inlineBtn">
                            <input id="postaviTrajanjeKolacica" type = "submit" value = "Postavi trajanje kolačića" class="button add inlineBtn"><br>
                        </div>

                        <div class = "textbox inlineWithBtn" id="kor_imeTextBox">
                            <input type = "text" name = "trajanjeSesije" id="trajanjeSesije" class="text" style="border: 1px solid #707070;" placeholder="Trajanje sesije"><br>
                        </div>
                        <div class="buttonWrapper inlineBtn">
                            <input id="postaviTrajanjeSesije" type = "submit" value = "Postavi trajanje sesije" class="button add inlineBtn"><br>
                        </div>
                        
                        <div class = "textbox inlineWithBtn" id="kor_imeTextBox">
                            <input type = "text" name = "stranicenje" id="stranicenje" class="text" style="border: 1px solid #707070;" placeholder="Straničenje"><br>
                        </div>
                        <div class="buttonWrapper inlineBtn">
                            <input id="postaviStranicenje" type = "submit" value = "Postavi straničenje" class="button add inlineBtn"><br>
                        </div>

                        <h2 style="margin-top: 50px;">Dnevnik rada</h2>
                        <hr>
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
                        <div class = "textbox" id="searchDnevnik">
                            <label for="search">Pretraži</label>
                            <input type = "text" name = "search" id="search" class="text" style="border: 1px solid #707070;"><br>
                        </div>
                        <table>
                            <thead>
                                <th>Ime korisnika</th>
                                <th>Naziv radnje</th>
                                <th>Radnja</th>
                            </thead>
                            <tbody id="dnevnikTbody">';
                            while($red = mysqli_fetch_assoc($rezultat2)){
                                    echo '
                                    <tr class="dnevnikRedak">
                                        <td>'.$red['ime'].'</td>
                                        <td>'.$red['naziv'].'</td>
                                        <td>'.$red['radnja'].'</td>
                                        <td style="display:none;">'.$red['upit'].'</td>
                                    </tr>';   
                            }
                            echo '
                            </tbody>
                        </table>
                        </div>
                        ';   
                    }
                ?>
                <div id="everyUser">
                    <span style = "float:left; margin-right: 15px; font-size: 20px;"> Noćni način rada </span>
                    <div class="checkboxContainer">
                        <input type="checkbox" class="checkbox" id="nocniNacinRada"/>
                        <span class="checkmark"></span>
                    </div>
                </div>
            </div>
        </main>  
        
        <?php
            require('komponente/podnozje.php');
        ?> 
        </div>
        <div id="snackbar"></div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="javascript/ivuk.js"></script>
</html>