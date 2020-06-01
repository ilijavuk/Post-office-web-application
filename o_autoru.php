<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>O autoru</title>
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
            <div id="wrapper" class="rotateIn"  style="overflow:visible;">
                <h1 class="heading">O autoru</h1>
                <div class="podatciWrapper" >    
                    <ul class="podatci">
                        <li>Ime i prezime:</li>
                        <li><b>Ilija Vuk</b></li>
                        <li>Matični broj</li>
                        <li><b>46198/17-R</b></li>
                        <li>Mail</li>
                        <li><b><a style="text-decoration: none;" href="mailto:ivuk@foi.hr?subject=Kontakt sa stranice o autoru" target="_blank">ivuk@foi.hr</a></b></li>
                        <li>Slika autora</li>
                        <li><img src="multimedija/autor-slika.jpg" alt="Autor" height=100 width=100 /></li>
                    </ul>
                    <div class="oAutoruTekst" >
                        <p>Ja sam Ilija Vuk.</p>
                        <p>Full stack developer iz Hrvatske.</p>
                        <p>S html-om, css-om i javascriptom sam se upoznao već u srednjoj školi. Od tad sam zavolio razvoj web stranica te mi je to najdraže područje u svijetu programiranja.</p>
                        <p>U srednjoj sam upoznat i sa MySQL-om i PHP-om. Taj dio mi je također zanimljiv te sam dosta vremena proveo u izradi baza podataka za različite projekte u srednjoj, na fakultetu, ali i u svoje slobodno vrijeme.</p>
                        <p>Neke od tih projekata možete vidjeti na mojem GitHub računu klikom na <a href="https://github.com/ivuk98" style="color: green; text-decoration: underline; cursor: pointer;">ovaj link</a>.</p>
                        <p>Ostali programski jezici kojima sam se bavio su Python 2.7 i 3.x, C, C++, C# te Java .</p>
                    </div>
                </div>
            </div>
        </main>  

        <?php
            require('komponente/podnozje.php');
        ?> 
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="javascript/ivuk.js"></script>
</html>