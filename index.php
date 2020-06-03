<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Početna stranica</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
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
                <div class="indexGrid">
                    <div class="istaknutaSekcija" onclick="location.href='uredi.php'">
                        Vršimo dostavu po cijeloj Europi i šire. <br>
                        Pogledaj lokacije naših ureda
                    </div>                

                    <div class="istaknutaSekcija" onclick="location.href='posiljke.php'">
                        Zanima te kako napreduje tvoja pošiljka? <br>
                        Pregledaj sve pošiljke
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr;">
                        <a href="dokumentacija.html" class="linkWithUnderline" style="display: block; font-size: 25px;">Dokumentacija</a>
                        <a href="dokumentacija.html" class="linkWithUnderline" style="display: block; text-align: right; font-size: 25px;">O autoru</a>
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
    <script src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
    <script src="javascript/ivuk.js"></script>
</html>