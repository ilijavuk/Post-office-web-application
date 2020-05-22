<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();

    $upit = "SELECT * FROM drzava;";
    $rezultat = $baza -> SelectDB($upit); 
    $baza -> zatvoriDB();
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Države</title>
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
            <a href="uredi.php" class="navLink">Uredi</a>
            <a href="drzave.php" class="navLink active">Države</a>
            <a href="o_autoru.html" class="navLink">O autoru</a>
            <a href="dokumentacija.html" class="navLink">Dokumentacija</a>
            <a href="register.html" class="navLink mobileOnly">Register</a>
            <a href="login.html" class="navLink mobileOnly">Login</a>
        </nav>

        <div class="footerWrapper">
		<main>
            <div id="wrapper" class="rotateIn">
                <h1 class="heading">Države</h1>
                <table>
                    <thead>
                        <th>Naziv</th>
                        <th>Skraćeni oblik</t>
                        <th>Produženi oblik</th>
                        <th>Članica EU</th>
                    </thead>
                    <tbody>
                        <?php
                            while($red = mysqli_fetch_assoc($rezultat)){
                                echo '
                                    <tr> 
                                        <td>'.$red['naziv'].'</td>
                                        <td>'.$red['skraceniOblik'].'</td>
                                        <td>'.$red['produzeniOblik'].'</td>
                                        <td>'.$red['clanEU'].'</td>
                                    </tr>
                                ';
                            }
                        ?>
                        <!-- SAMO ZA ADMINE -->
                        <tr>
                            <td><input type="textbox" class="tableInput" id="naziv"></td>
                            <td><input type="textbox" class="tableInput" id="skraceniOblik"></td>
                            <td><input type="textbox" class="tableInput" id="produzeniOblik"></td>
                            <td>
                                <select class="tableInput" id="clanEU">
                                <option value="0">False</option>
                                <option value="1">True</option>
                            </td>
                        </tr>
                        <!--                -->
                    </tbody>
                </table>
                <div class="buttonWrapper">
                    <input id="submitBtn" type = "submit" value = "Add" class="submit" style="margin-top: 15px; width: 150px;"><br>
                </div>
            </div>
        </main>  

        <footer class="footer">
            <span class="footerText">2020, Vuk Ilija</span>+
        </footer>  
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="javascript/ivuk.js"></script>
</html>