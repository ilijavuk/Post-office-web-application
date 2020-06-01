<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Zaboravljena lozinka</title>
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

        <nav class="navBar">
            <a href="posiljke.php" class="navLink">Pošiljke</a>
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
            <div id="wrapper" class="rotateIn passResetWrapper" style="width: 80%;">
                <h1 class="heading">Zaboravljena lozinka</h1>
                <div class = "textbox" id="e_mailTextBox">
                    <label for="email">E-mail</label>
                    <input type = "email" name = "email" id="email" class="text"><br>
                </div>
                <div class = "textbox" id="passTextBox" style="display: none;">
                    <label for="code" id="codeLabel">Code</label>
                    <input type = "text" name = "code" id="code" class="text"><br>
                </div>
                <div class = "textbox" id="newPassTextBox" style="display: none;">
                    <label for="pass" id="codeLabel">Nova lozinka</label>
                    <input type = "password" name = "pass" id="pass" class="text"><br>
                </div>
                <div class="buttonWrapper">
                    <input id="posaljiLozinku" type = "submit" value = "Pošalji" class="submit"><br>
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