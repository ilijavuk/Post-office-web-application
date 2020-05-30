<?php
    require("baza.class.php"); 
    $baza = new Baza;
    $baza -> spojiDB();
    session_start();
    if(!isset($_SESSION['uloga'])){
        $_SESSION['uloga'] = 0;
    }

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
                        <?php
                        if($_SESSION['uloga'] == 3){
                            echo '
                            <tr>
                                <td><input type="textbox" class="tableInput" id="naziv"></td>
                                <td><input type="textbox" class="tableInput" id="skraceniOblik"></td>
                                <td><input type="textbox" class="tableInput" id="produzeniOblik"></td>
                                <td>
                                    <select class="tableInput" id="clanEU">
                                    <option value="0">False</option>
                                    <option value="1">True</option>
                                </td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                if($_SESSION['uloga'] == 3){
                    echo '<div class="buttonWrapper">
                        <input id="submitBtn" type = "submit" value = "Add" class="submit" style="margin-top: 15px; width: 150px;"><br>
                    </div>';
                }
            ?>
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