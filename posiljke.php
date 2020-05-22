<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();

    $uloga = 3;
    $kor_id = 1;


    $rezultat2 = nulL;
    if($uloga != null && $uloga >= 1){
        $upit = "SELECT id_primatelja, spremnaZaIsporuku, cijenaPoKg, masa, t2.ime_primatelja, t3.naziv AS trenutni_ured FROM posiljka AS t1 LEFT JOIN ( SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime_primatelja FROM korisnik ) AS t2 ON t1.id_primatelja = t2.korisnik_id LEFT JOIN postanskiured AS t3 ON t1.id_trenutniUred=t3.postanskiUred_id WHERE t1.id_posiljatelja =".$kor_id."";
        $rezultat2 = $baza -> SelectDB($upit);
        $upit = "SELECT spremnaZaIsporuku, cijenaPoKg, masa, t2.ime_posiljatelja, t3.naziv AS trenutni_ured FROM posiljka AS t1 LEFT JOIN ( SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime_posiljatelja FROM korisnik ) AS t2 ON t1.id_posiljatelja = t2.korisnik_id LEFT JOIN postanskiured AS t3 ON t1.id_trenutniUred=t3.postanskiUred_id WHERE t1.id_primatelja=".$kor_id.";";
        $rezultat3 = $baza -> SelectDB($upit);
    }
    $upit = "SELECT * FROM posiljka AS t1 LEFT JOIN ( SELECT id_posiljka, slika, dopustenjeZaObjavu FROM racun ) AS t2 ON t1.posiljka_id=t2.id_posiljka WHERE dopustenjeZaObjavu = 1;";
    $rezultat = $baza -> SelectDB($upit);  
    

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
                <?php
                    if($uloga != null && $uloga >= 1){
                        echo '
                        <div class="switchShowingWrapper posiljkeSwitch">
                            <div id="showingLeft" class="switchShowing activeShow">Galerija</div>
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
                <div class="gallery">
                    <?php
                         while($red = mysqli_fetch_assoc($rezultat)){
                            echo   '<figure class="galleryFigure"><img src="'.$red['slika'].'" style="width:100%;"/></figure>';
                        }
                    ?>
                </div>
                
                <?php 
                    if($uloga != null && $uloga >= 1){
                        //prikaz šaljem/primam pošiljki
                        
                        //šaljem
                        echo '<div id="saljemPrimamWrapper" style="display:none;">
                                <h2>Pošiljke koje šaljem</h2>
                                <hr>
                                <table>
                                <thead>
                                    <th>Ime primatelja</th>
                                    <th>Trenutni ured</th>
                                    <th>Cijena po kg</th>
                                    <th>Masa</th>
                                    <th>Spremna za isporuku</th>
                                </thead>
                            <body>';

                            while($red = mysqli_fetch_assoc($rezultat2)){
                                echo  ' <tr>
                                <td>'.$red['ime_primatelja'].'</td>
                                <td>'.$red['trenutni_ured'].'</td>
                                <td>'.$red['cijenaPoKg'].'</td>
                                <td>'.$red['masa'].'</td>
                                <td>'.$red['spremnaZaIsporuku'].'</td>
                            </tr>';
                            }

                            echo '</tbody>
                            </table>';

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
                                echo  ' <tr>
                                <td>'.$red['ime_posiljatelja'].'</td>
                                <td>'.$red['trenutni_ured'].'</td>
                                <td>'.$red['cijenaPoKg'].'</td>
                                <td>'.$red['masa'].'</td>
                                <td>'.$red['spremnaZaIsporuku'].'</td>
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
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="javascript/ivuk.js"></script>
</html>