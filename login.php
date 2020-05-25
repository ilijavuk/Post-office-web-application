<!DOCTYPE html>
<html lang="hr">
    <head>
        <?php
            session_start();
            if(isset($_SESSION['uloga']) && $_SESSION['uloga'] > 0){
                header('Location: ./index.php');
            }
        ?>
        <title>Prijava</title>
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
       <?php
            require('komponente/header.php');
            require('komponente/navBar.php');
       ?>
        
        <div class="footerWrapper">
		<main>
            <div id="wrapper" class="rotateIn">
                <h1 class="heading">Prijavi se</h1>
                <h2 id="greska"></h2>
                <a class="linkWithUnderline redirect" href="login.html">Nemaš profil? Registriraj se</a>
                    <div class = "textbox" id="korisnicko_imeTextBox">
                        <label for="korisnicko_ime">Korisničko ime</label>
                        <input type = "text" name = "korisnicko_ime" id="korisnicko_ime" class="text"><br>
                    </div>
                    <div class = "textbox" style="display: none;" id="emailTextBox">
                        <label for="email">E-mail</label>
                        <input type = "email" name = "email" id="email" class="text"><br>
                    </div>
                    <div class = "textbox">
                        <label for="lozinka">Lozinka</label>
                        <input type = "password" name = "lozinka" id="lozinka" class="text"><br>
                    </div>
                    <span class="linkWithUnderline" id="forgottenUsername" style="text-decoration: underline; display: block;">Zaboravili ste korisničko ime? Prijavite se pomoću e-maila</span>
                    <a class="linkWithUnderline" href="passwordRecovery.html">Zaboravili ste lozinku?</a>
                    <div class="buttonWrapper">
                        <input id="submitBtn" type = "submit" value = "Submit" class="submit"><br>
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