<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();
    session_start();

    $upit = "SELECT drzava_id, naziv FROM drzava;";
    $rezultat1 = $baza -> SelectDB($upit);

    $upit = "SELECT * FROM postanskiured AS t1 LEFT JOIN (SELECT id_pocetniUred, COUNT(*) AS 'broj_poslanih' FROM posiljka GROUP BY id_pocetniUred) AS t2 ON  t1.postanskiUred_id = t2.id_pocetniUred INNER JOIN ( 
        SELECT postanskiUred_id, t4.broj_primljenih FROM postanskiured AS t3 LEFT JOIN (SELECT id_konacniUred, COUNT(*) AS 'broj_primljenih' FROM posiljka GROUP BY id_konacniUred) AS t4 ON  t3.postanskiUred_id = t4.id_konacniUred) AS q2 ON q2.postanskiUred_id=t1.postanskiUred_id";
    $rezultat2 = $baza -> SelectDB($upit);

    $upit = "SELECT korisnik_id, ime, prezime, korisnicko_ime FROM korisnik WHERE id_uloga='2';";
    $rezultat3 = $baza -> SelectDB($upit);    

    $baza -> zatvoriDB();
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Uredi</title>
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
            <a href="posiljke.php" class="navLink">Pošiljke</a>
            <a href="racuni.php" class="navLink">Računi</a>
            <a href="uredi.php" class="navLink active">Uredi</a>
            <a href="drzave.php" class="navLink">Države</a>
            <a href="o_autoru.html" class="navLink">O autoru</a>
            <a href="dokumentacija.html" class="navLink">Dokumentacija</a>
            <a href="register.html" class="navLink mobileOnly">Register</a>
            <a href="login.html" class="navLink mobileOnly">Login</a>
        </nav>

        <div class="footerWrapper">
		<main>
            <div id="wrapper">
                <h1 class="heading">Uredi</h1>
                <div class="switchShowingWrapper posiljkeSwitch" style="border: none;">
                    <div class = "textbox" id="kor_imeTextBox">
                        <label for="select">Država</label>
                        <select class="select-css" id="select" style="width: 100%; height: 42px;">
                            <option value="-1">SVE</option>
                            <?php
                                while($red = mysqli_fetch_assoc($rezultat1)){
                                    echo '
                                        <option value = '.$red["drzava_id"].'> 
                                            '.$red["naziv"].'
                                        </option>
                                    ';
                                }
                            ?>
                        </select> 
                    
                    </div>  
                    <div class = "textbox" id="kor_imeTextBox">
                        <label for="search">Pretraži</label>
                        <input type = "text" name = "search" id="search" class="text" style="border: 1px solid #707070;"><br>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <th>Naziv</th>
                        <th>Adresa</t>
                        <th>Poštanski broj</th>
                        <th id="broj_poslanih">Broj poslanih</th>
                        <th id="broj_primljenih">Broj primljenih</th>
                    </thead>
                    <tbody>
                        <?php
                            while($red = mysqli_fetch_assoc($rezultat2)){
                                echo '
                                    <tr class="postanskiUred" style="cursor:pointer;"> 
                                        <td>'.$red['naziv'].'</td>
                                        <td>'.$red['adresa'].'</td>
                                        <td>'.$red['postanskiBroj'].'</td>
                                        <td style="display:none">'.$red['id_drzave'].'</td>
                                        <td>'.$red['broj_poslanih'].'</td>
                                        <td>'.$red['broj_primljenih'].'</td>
                                        <td style="display:none;">'.$red['postanskiUred_id'].'</td>
                                    </tr>
                                ';   
                            }
                        ?>
                        
                         <?php 
                         if($_SESSION['uloga']  == 3){
                             echo '
                            <tr id="elementZaDodavanje">
                                <td><input type="textbox" class="tableInput" id="naziv"></td>
                                <td><input type="textbox" class="tableInput" id="adresa"></td>
                                <td>
                                    <input type="textbox" class="tableInput" id="poštanskiBroj">
                                </td>
                                <td>  <select class="select-css" id="select" style="height: 29px; width: 100%;">';
                                            $rezultat1->data_seek(0);
                                            while($red = mysqli_fetch_assoc($rezultat1)){
                                                echo '
                                                    <option value = '.$red["drzava_id"].'> 
                                                        '.$red["naziv"].'
                                                    </option>
                                                ';
                                            }
                                        
                                echo '
                                    </select> 
                                </td>
                                <td>
                                    <select class="tableInput" id="moderator">';

                                        while($red = mysqli_fetch_assoc($rezultat3)){
                                            echo '
                                                <option value = '.$red["korisnik_id"].'> 
                                                    '.$red["ime"].' '.$red["prezime"].'('.$red["korisnicko_ime"].')
                                                </option>';
                                        }
                                echo '</td></tr>';
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                    if($_SESSION['uloga']  == 3){
                    echo '<div class="buttonWrapper">
                        <input id="submitBtn" type = "submit" value = "Submit" class="submit" style="margin-top: 15px;"><br>
                    </div>';}
                ?>
            </div>
        </main>  

        <footer class="footer">
            <span class="footerText">2020, Vuk Ilija</span>+
        </footer>  
        </div>
        <div id="overlay">
        </div>
        <div class="modal" style="display:none; width: 90%; left: 5%;">
            <div class="gallery" id="gallery">
               
            </div>
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="javascript/ivuk.js"></script>
</html>