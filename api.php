<?php

    if(isset($_GET['fetch_postanskiUred'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT * FROM postanskiured AS t1 LEFT JOIN (SELECT id_pocetniUred, COUNT(*) AS 'broj_poslanih' FROM posiljka GROUP BY id_pocetniUred) AS t2 ON  t1.postanskiUred_id = t2.id_pocetniUred INNER JOIN (SELECT postanskiUred_id, t4.broj_primljenih FROM postanskiured AS t3 LEFT JOIN (SELECT id_konacniUred, COUNT(*) AS 'broj_primljenih' FROM posiljka GROUP BY id_konacniUred) AS t4 ON  t3.postanskiUred_id = t4.id_konacniUred) AS q2 ON q2.postanskiUred_id=t1.postanskiUred_id";
        $rezultat = $baza -> SelectDB($upit);
        
        while($red = mysqli_fetch_assoc($rezultat)){
            $redovi[] = $red;
            /*
                <tr> 
                    <td>'.$red['naziv'].'</td>
                    <td>'.$red['adresa'].'</td>
                    <td>'.$red['postanskiBroj'].'</td>
                    <td style="display:none">'.$red['id_drzave'].'</td>
                    <td>'.$red['broj_poslanih'].'</td>
                    <td>'.$red['broj_primljenih'].'</td>
                </tr>
            ';*/
        }
        echo json_encode($redovi);
    }

    if(isset($_GET['insert_postanskiUred'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 3){
                    
            $_POST['id_moderatora'] = filter_input(INPUT_POST, 'id_moderatora', FILTER_SANITIZE_NUMBER_INT);
            $_POST['id_drzave'] = filter_input(INPUT_POST, 'id_drzave', FILTER_SANITIZE_NUMBER_INT);
            $_POST['naziv'] = filter_input(INPUT_POST, 'naziv', FILTER_SANITIZE_STRING);
            $_POST['adresa'] = filter_input(INPUT_POST, 'adresa', FILTER_SANITIZE_STRING);
            $_POST['postanskiBroj'] = filter_input(INPUT_POST, 'postanskiBroj', FILTER_SANITIZE_NUMBER_INT);
            
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "INSERT INTO postanskiured (id_moderatora, id_drzave, naziv, adresa, postanskiBroj) VALUES (?, ?, ?, ?, ?);";
            $stmt = $veza -> prepare($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("iisss", $_POST['id_moderatora'], $_POST['id_drzave'], $_POST['naziv'], $_POST['adresa'], $_POST['postanskiBroj']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode("Uspjeh");
                    zapisiULog($upit, '2', $veza,'poštanski ured');
                }
                else{
                    echo json_encode("Neuspjeh");
                }
            }
        }
    }

    if(isset($_GET['insert_drzava'])){
        if(isset($_POST['naziv']) && isset($_POST['skraceniOblik']) && isset($_POST['produzeniOblik']) && isset($_POST['clanEU'])  && strlen($_POST['naziv']) > 0 && strlen($_POST['skraceniOblik']) > 0 &&  strlen($_POST['produzeniOblik']) > 0 ){
            $_POST['naziv'] = filter_input(INPUT_POST, 'naziv', FILTER_SANITIZE_STRING);
            $_POST['skraceniOblik'] = filter_input(INPUT_POST, 'skraceniOblik', FILTER_SANITIZE_STRING);
            $_POST['produzeniOblik'] = filter_input(INPUT_POST, 'produzeniOblik', FILTER_SANITIZE_STRING);
            $_POST['clanEU'] = filter_input(INPUT_POST, 'clanEU', FILTER_SANITIZE_NUMBER_INT);

            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "INSERT INTO drzava (naziv, skraceniOblik, produzeniOblik, clanEU) VALUES (?, ?, ?, ?);";
            $stmt = $veza -> prepare($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("sssi", $_POST['naziv'], $_POST['skraceniOblik'], $_POST['produzeniOblik'], $_POST['clanEU']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode("Uspjeh");
                    zapisiULog($upit, '2', $veza, 'drzava');
                }
                else{
                    echo json_encode("Neuspjeh");
                }
            }
        }
    }

    if(isset($_GET['fetch_drzaveStatistika'])){
        require_once("baza.class.php");
        session_start();

        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT t1.naziv, COUNT(t3.posiljka_id) as broj_posiljki, SUM(case t4.placen when '1' then 1 else 0 end) AS broj_placenih FROM drzava AS t1 LEFT JOIN postanskiured AS t2 ON t1.drzava_id=t2.id_drzave LEFT JOIN posiljka AS t3 ON t2.postanskiUred_id=t3.id_pocetniUred LEFT JOIN racun AS t4 ON t3.posiljka_id=t4.id_posiljka WHERE t3.vrijeme_slanja >= ? AND t3.vrijeme_slanja <= ? AND t2.id_moderatora = ? GROUP BY t1.naziv;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("ssi", $_POST['od'], $_POST['do'], $_SESSION['kor_id']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }
            
            while ($red = $rezultat->fetch_assoc())
            {
                $redovi[] = $red;
            }
            echo json_encode($redovi);
            zapisiULog($upit, '2', $veza, 'statistika drzave'); 
        }        
    }
    
    if(isset($_GET['fetch_drzaveKratice'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] >= 2){
            require_once("baza.class.php");
            $baza = new Baza;
            $baza -> spojiDB();
            $rezultat = $baza -> SelectDB("SELECT naziv, skraceniOblik FROM drzava;");
            $data = array();
            while($red = mysqli_fetch_assoc($rezultat)){
                $tmp = array();

                $tmp[] = $red['naziv'];
                $tmp[] = $red['skraceniOblik'];

                $data[] = $tmp;
            }
            echo json_encode($data);
        }
    }

    if(isset($_GET['insert_racun'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit1 = "SELECT t1.id_moderatora FROM postanskiured as t1 LEFT JOIN posiljka as t2 ON t1.postanskiUred_id=t2.id_konacniUred WHERE posiljka_id = ?";
        $stmt = $veza->prepare($upit1);
        if($stmt == null){
            echo json_encode('Neuspješno dohvaćanje id-a moderatora');
        }
        else{
            $stmt -> bind_param("i", $_POST['id_posiljka']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }

            if ($red = $rezultat->fetch_assoc())
            {
                $id_moderatora = $red['id_moderatora'];
                $upit2 = "INSERT INTO racun (id_posiljka, id_moderatora, vrijemeIzdavanja, rokZaPlacanje, placen, iznos) VALUES (?, ?, ?, ?, '0', ?);";
                $stmt = $veza -> prepare($upit2);
                if($stmt == null){
                   echo json_encode('Neuspjeh $stmt = null');
                   }
                   else{
                       $currTime = date("Y-m-d H:i:s", strtotime(dohvatiPomak()." hours"));
                       $rokZaPlacanje = date("Y-m-d H:i:s", strtotime( "$currTime + 7 days" ));
                       $stmt -> bind_param("iissi", $_POST['id_posiljka'], $id_moderatora, $currTime, $rokZaPlacanje, $_POST['iznos']);
                       $stmt -> execute();
                       if($stmt->affected_rows > 0){
                            echo json_encode("Uspjeh");
                            zapisiULog($upit1, '2', $veza, 'račun');  
                            zapisiULog($upit2, '2', $veza, 'račun');                        
                    }
                    else{
                       echo json_encode("Neuspjeh");
                    }
                }
            }
            else{
                echo json_encode('0 redova dohvaćeno');
            }
        }       
    }

    if(isset($_GET['update_racun'])){
        if(isset($_POST['slika']) && isset($_POST['racun_id']) && isset($_POST['dopustenje']) && strlen($_POST['slika']) > 0 && strlen($_POST['racun_id']) > 0 ){   
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE racun SET placen='1', slika=?, dopustenjeZaObjavu=? WHERE racun_id = ?;";
            $stmt = $veza -> prepare($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("sii", $_POST['slika'], $_POST['dopustenje'], $_POST['racun_id']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode("Uspjeh");
                    zapisiULog($upit, '2', $veza, 'račun');  
                }
                else{
                    echo json_encode($_POST['slika']." ".$_POST['racun_id']." ".$_POST['dopustenje']);
                    echo json_encode("Neuspjeh");
                }
            }
        }
        else{
            echo json_encode("Niste popunili sva polja");
        }
    }

    if(isset($_GET['update_racunDodajIznos'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE racun SET puniIznos=(iznos+?) WHERE racun_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh 1');
        }
        else{
            $stmt -> bind_param("ss", $_POST['iznos_obrade'], $_POST['racun_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");
                zapisiULog($upit, '2', $veza, 'račun dodaj iznos');  
            }
            else{
                echo json_encode("Neuspjeh 2");
            }
        }
    }

    if(isset($_GET['fetch_role'])){
        session_start();
        echo $_SESSION['uloga'];
    }

    if(isset($_GET['fetch_korisnikFromRacun'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime FROM korisnik AS t1 LEFT JOIN posiljka AS t2 ON t1.korisnik_id=t2.id_primatelja LEFT JOIN racun AS t3 ON t2.posiljka_id=t3.id_posiljka WHERE racun_id = ?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("i", $_POST['racun_id']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }
            
            if ($red = $rezultat->fetch_assoc()){
                echo  json_encode($red);
                zapisiULog($upit, '2', $veza, 'korisnik from račun');  
            }
            else{
                echo json_encode('0 rows fetched');
            }
        }       
    }

    if(isset($_GET['update_korisnikBlock'])){
        $datum = date("Y-m-d H:i:s", strtotime("+7 days + ".dohvatiPomak()." hours"));
        
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE korisnik SET blokiranDo=?, id_status=3 WHERE korisnik_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("si", $datum, $_POST['korisnik_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");
                zapisiULog($upit, '2', $veza, 'korisnik block');  
            }
            else{
                echo json_encode("Neuspjeh");
            }
        }
    }

    if(isset($_GET['update_korisnikUnblock'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE korisnik SET blokiranDo=NULL, id_status=2, neuspjeliLogin=0 WHERE korisnik_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("i", $_POST['korisnik_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");
                zapisiULog($upit, '2', $veza, 'korisnik unblock');
            }
            else{
                echo json_encode("Neuspjeh");
            }
        }
    }

    if(isset($_GET['update_korisnikGiveModerator'])){
        session_start();
        if($_SESSION['uloga'] == 3){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE korisnik SET id_uloga=2 WHERE korisnik_id = ?;";
            $stmt = $veza -> prepare($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("i", $_POST['korisnik_id']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode('Uspjeh');
                    zapisiULog($upit, '2', $veza, 'korisniku dodijeljen moderator');
                }
                else{
                    echo json_encode('Neuspjeh');
                }
            }
        }
    }

    if(isset($_GET['insert_posiljka'])){
        session_start();
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "INSERT INTO posiljka (id_posiljatelja, id_primatelja, masa, vrijeme_slanja) VALUES (?, ?, ?, CURDATE());";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh $stmt = null');
        }
        else{
            $stmt -> bind_param("iii", $_SESSION['kor_id'], $_POST['id_primatelja'], $_POST['masa']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");
                zapisiULog($upit, '2', $veza, 'pošiljka');  
            }
            else{
                echo json_encode("Neuspjeh");
            }
        }
    }

    if(isset($_GET['update_posiljkaAktiviraj'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE posiljka SET id_pocetniUred=?, id_konacniUred=?, id_trenutniUred=?, cijenaPoKg=? WHERE posiljka_id=?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("iiisi", $_POST['id_pocetniUred'], $_POST['id_konacniUred'], $_POST['id_pocetniUred'], $_POST['cijenaPoKg'], $_POST['posiljka_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");                
            }
            else{
                echo json_encode("Neuspjeh didnt get post");
            }
        }
    }

    if(isset($_GET['update_posiljkaProslijedi'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        if($_GET['update_posiljkaProslijedi'] == 1){
            $upit = "UPDATE posiljka SET spremnaZaIsporuku=1 WHERE posiljka_id=?;";
            $stmt = $veza -> prepare($upit);
        }
        else if($_GET['update_posiljkaProslijedi'] == 2){
            $upit = "UPDATE posiljka SET id_trenutniUred=? WHERE posiljka_id=?;";
            $stmt = $veza -> prepare($upit);
        
        }
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            if($_GET['update_posiljkaProslijedi'] == 1){
                $stmt -> bind_param("i", $_POST['posiljka_id']);
            }
            else if($_GET['update_posiljkaProslijedi'] == 2){
                $stmt -> bind_param("ii", $_POST['id_trenutniUred'], $_POST['posiljka_id']);
            }
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");
                zapisiULog($upit, '2', $veza, 'pošiljka proslijedi');  
            }
            else{
                echo json_encode("Neuspjeh didnt get post");
            }
        }
    }

    if(isset($_GET['fetch_korisnickoIme'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT * FROM korisnik WHERE korisnicko_ime = ?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("s", $_POST['korisnicko_ime']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }

            if ($red = $rezultat->fetch_assoc())
            {
                echo json_encode('1');
                zapisiULog($upit, '2', $veza, ' korisnik');  
            }
            else{
                echo json_encode('0');
            }
        }       
    }

    if(isset($_GET['insert_korisnik'])){
        if(!(isset($_POST['ime']) && isset($_POST['prezime']) && isset($_POST['korisnicko_ime']) && isset($_POST['email']) && isset($_POST['lozinka']))){
            echo json_encode('Niste popunili sva polja');
            die;
        }
        if(strlen($_POST['korisnicko_ime']) < 3){
            echo json_encode('Korisničko ime je prekratko');
            die;
        }
        if(preg_match("/^\S+@\S+\.\S+$/", $_POST['email']) == false){
            echo json_encode('Email nije u ispravnom formatu');
            die;
        }
        if(strlen($_POST['lozinka']) < 8){
            echo json_encode('Lozinka je prekratka');
            die;
        }

        $lozinka_sha1 = sha1($_POST['lozinka']);
        $link_aktivacije = sha1($_POST['korisnicko_ime']);

        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka, lozinka_sha1, email, linkAktivacije) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh $stmt = null');
        }
        else{
            $stmt -> bind_param("sssssss", $_POST['ime'], $_POST['prezime'], $_POST['korisnicko_ime'], $_POST['lozinka'], $lozinka_sha1, $_POST['email'], $link_aktivacije);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo json_encode("Uspjeh");
                zapisiULog($upit, '2', $veza, 'korisnik');  
                $to = $_POST['email'];
                $subject = 'POŠTE Aktivacijski link';
                $message = '
                <html>
                <head>
                    <title>Aktivacijski link</title>
                    <style>
                    body{
                        font-family: Nunito, Roboto, Segoe UI;
                        font-size: 22px;
                        width: 100%;
                        height: 500px;
                        background-color: #707070;
                        overflow: hidden;
                    }

                    .wrapper{
                        position: absolute;
                        left: 50%;
                        top: 0;
                        transform: translateX(-50%);
                        background-color: #FFF;
                        height: 100%;
                        width: 600px;
                    }

                    main{
                        position: absolute;
                        top: 70px;
                        padding: 15px;
                    }
                    
                    .header{
                        width: 600px;
                        height: 85px;
                        border: solid 1px #707070;
                        background-color: #232323;
                    }

                    .headerText{
                        position: relative;
                        bottom: 7px;
                        font-size: 30px;
                        color: #f1cd7b;
                        display: block;
                        text-align: center;
                    }

                    .headerFigure{
                        display: block;
                        margin: auto;
                        width: 83px;
                        object-fit: contain;
                        position: relative;
                        top: 5px;
                    }

                    .headerImage{
                        width: 100%;
                    }

                    .boldedText{
                        font-weight: bold;
                    }
                    
                    a{
                        text-decoration: none;
                        color: green;
                    }
                    
                    a:hover{
                        text-decoration: underline;
                        cursor: pointer;
                    }

                    </style>
                </head>
                <body>
                    <div class="wrapper">
                    <header class="header">
                    <figure class="headerFigure"><a href="index.php"><img src="http://barka.foi.hr/WebDiP/2019_projekti/WebDiP2019x144/multimedija/post-icon.png" class="headerImage"></a></figure>
                    <span class="headerText">POŠTE</span>
                    </header>
                    <main>
                    <p class="boldedText">Poštovani '.$_POST['ime'].' '.$_POST['prezime'].', </p>
                    <p>
                        Nedavno ste kreirali račun na našoj stranici. Ovim putem Vas pozdravljamo te Vam želimo ugodan ostanak na našoj stranici.
                    </p>
                    <p>
                    Kliknite <a href="http://barka.foi.hr/WebDiP/2019_projekti/WebDiP2019x144/api.php?aktivacija='.$link_aktivacije.'" boldedText>OVDJE</a> kako bi aktivirali svoj račun
                    </p>
                    </main>
                    </div>

                    </body>
                </html>
                ';
                        
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                $headers[] = 'From: Poste@foi.hr' . "\r\n" .
                    'Reply-To: Poste@foi.hr' . "\r\n";

                mail($to, $subject, $message, implode("\r\n", $headers));
            }
            else{
                echo json_encode("Neuspjeh");
            }
        }
    }

    if(isset($_GET['aktivacija'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT * FROM korisnik WHERE linkAktivacije = ?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo json_encode('Neuspjeh');
        }
        else{
            $stmt -> bind_param("s", $_GET['aktivacija']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }

            if ($red = $rezultat->fetch_assoc())
            {
                echo json_encode('1');
                $upit = "UPDATE korisnik SET id_status = 2, linkAktivacije='' WHERE linkAktivacije = ?;";
                $stmt = $veza -> prepare ($upit);
                $stmt -> bind_param("s", $_GET['aktivacija']);
                $stmt -> execute();
                zapisiULog($upit, '2', $veza, 'korisnik activate'); 
                header("Location: login.php"); 
            }
            else{
                echo json_encode('0');
            }
        }       
    }

    if(isset($_GET['login'])){
        if(isset($_POST['lozinka']) && strlen($_POST['lozinka']) >= 8){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $rezultat = $baza->SelectDB("SELECT brojPokusaja FROM postavke;");
            $brojPokusaja = mysqli_fetch_assoc($rezultat)['brojPokusaja'];
            $lozinka_sha1 = sha1($_POST['lozinka']);
            session_start();
    
            if(isset($_POST['korisnicko_ime']) && strlen($_POST['korisnicko_ime']) >= 3){
                $upit1 = "SELECT uvjeti, korisnicko_ime, korisnik_id, id_uloga, id_status, blokiranDo, neuspjeliLogin, lozinka_sha1 FROM korisnik WHERE korisnicko_ime = ?;";
                $stmt = $veza -> prepare ($upit1) or trigger_error($veza->error, E_USER_ERROR);
                $stmt -> bind_param("s", $_POST['korisnicko_ime']);
            }
            else if(isset($_POST['email']) && preg_match("/^\S+@\S+\.\S+$/", $_POST['email']) == true)
            {
                $upit1 = "SELECT uvjeti, korisnicko_ime, korisnik_id, id_uloga, id_status, blokiranDo, neuspjeliLogin, lozinka_sha1 FROM korisnik WHERE email = ?;";
                $stmt = $veza -> prepare ($upit1);
                $stmt -> bind_param("s", $_POST['email']);
            }
            else{
                echo json_encode('Provjera nije prošla korisničko ime/email');
                die;
            }
    
            $stmt -> execute();
    
            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }
    
            if ($red = $rezultat->fetch_assoc())
            {
                if($red['id_status'] == 1){
                    echo json_encode('Vaš račun nije aktiviran! Aktivacijska poruka Vas čeka u vašoj e-pošti');
                    die;
                }
                if($red['neuspjeliLogin'] >= $brojPokusaja){
                    echo json_encode('Zaključani ste!',JSON_UNESCAPED_UNICODE);
                }
                else if($red['lozinka_sha1'] == $lozinka_sha1){
                    $_SESSION['kor_id'] = $red['korisnik_id'];
                    $_SESSION['uloga'] = $red['id_uloga'];
                    if($red['id_status'] == 3 && strtotime($red['blokiranDo']) > strtotime(dohvatiPomak()." hours")){
                        echo 'nije proslo';
                        $_SESSION['blokiran'] = "1";
                    }
                    else if($red['id_status'] == 3 && strtotime($red['blokiranDo']) < strtotime(dohvatiPomak()." hours")){
                        echo 'proslo';
                        $upit3 = "UPDATE korisnik SET blokiranDo = NULL, id_status = 2 WHERE korisnicko_ime = ?";
                        $stmt = $veza -> prepare ($upit3);
                        $stmt -> bind_param("s", $_POST['korisnicko_ime']);
                        $stmt -> execute();
                    }

                    if($red['uvjeti'] == 0){
                        unset($_COOKIE['uvjetiKoristenja']); 
                        setcookie('uvjetiKoristenja', null, -1, '/'); 
                    }
    
                    if(isset($_POST['korisnicko_ime'])){
                        $upit2 = "UPDATE korisnik SET neuspjeliLogin = 0 WHERE korisnicko_ime = ?";
                        $stmt = $veza -> prepare ($upit2);
                        $stmt -> bind_param("s", $_POST['korisnicko_ime']);
                    }
                    else if(isset($_POST['email'])){
                        $upit2 = "UPDATE korisnik SET neuspjeliLogin = 0 WHERE email = ?";
                        $stmt = $veza -> prepare ($upit2);
                        $stmt -> bind_param("s", $_POST['email']);
                    } 
                    $stmt -> execute();
                    echo  json_encode('1'.$red['korisnicko_ime']);
                    $_SESSION['LAST_LOGIN'] = time(); 
                }
                else{
                    if(isset($_POST['korisnicko_ime'])){
                        $upit2 = "UPDATE korisnik SET neuspjeliLogin = neuspjeliLogin + 1 WHERE korisnicko_ime = ?";
                        $stmt = $veza -> prepare ($upit2);
                        $stmt -> bind_param("s", $_POST['korisnicko_ime']);
                    }
                    else if(isset($_POST['email'])){
                        $upit2 = "UPDATE korisnik SET neuspjeliLogin = neuspjeliLogin + 1 WHERE email = ?";
                        $stmt = $veza -> prepare ($upit2);
                        $stmt -> bind_param("s", $_POST['email']);
                    }
                    $stmt -> execute();
                    echo json_encode('0');
                    zapisiULog($upit2, '1', $veza, 'login');  
                }   
            }
            zapisiULog($upit1, '1', $veza, 'login');  
        }
        else{
            echo json_encode('Provjera nije prošla lozinka');
        }
    }

    if(isset($_GET['logout'])){
        session_start();
        session_unset();
        session_destroy(); 
        header('Location: login.php'); 

        $upit = "INSERT LOGOUT";
    
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        zapisiULog($upit, '1', $veza, 'logout');    
    }

    if(isset($_GET['fetch_galerija'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT t2.slika FROM posiljka AS t1 RIGHT JOIN racun AS t2 ON t1.posiljka_id=t2.id_posiljka WHERE dopustenjeZaObjavu = 1 AND slika IS NOT NULL AND t1.id_konacniUred=?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
                echo json_encode('Neuspjeh');
            }
        else{
            $stmt -> bind_param("i", $_POST['postanskiUred_id']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo json_encode($stmt->errno);
            }

            $redovi = [];
            while ($red = $rezultat->fetch_assoc())
            {
                $redovi[] = $red['slika'];
            }
            echo json_encode($redovi);
            zapisiULog($upit, '2', $veza, 'galerija'); 
        }   
    }

    if(isset($_GET['check_timeout'])){
        session_start();
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT trajanjeSesije FROM postavke;";
        $rezultat = $baza -> SelectDB($upit);
        $trajanjeSesije = null;
        if($red = mysqli_fetch_assoc($rezultat)){
            $trajanjeSesije = 3600 * $red['trajanjeSesije'];
        }
        
        if (isset($_SESSION['LAST_LOGIN']) && (time() - $_SESSION['LAST_LOGIN'] > $trajanjeSesije)) {
            session_unset();
            session_destroy();
        }
    }

    if(isset($_GET['fetch_trajanjeKolacica'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT trajanjeKolacica FROM postavke;";
        $rezultat = $baza -> SelectDB($upit);

        if($red = mysqli_fetch_assoc($rezultat)){
            $trajanjeKolacica = 3600 * $red['trajanjeKolacica'];
            echo json_encode($trajanjeKolacica);
        }
    }

    if(isset($_GET['fetch_stranicenje'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT stranicenje FROM postavke;";
        $rezultat = $baza -> SelectDB($upit);

        if($red = mysqli_fetch_assoc($rezultat)){
            echo json_encode($red['stranicenje']);
        }
    }

    if(isset($_GET['fetch_tema'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        if(isset($_POST['fetchNightMode'])){
            $upit = "SELECT * FROM tema WHERE naziv = 'Dark';";
        }
        else{
            $upit = "SELECT * FROM tema INNER JOIN postavke ON postavke.tema=tema.tema_id;";
        }
        $rezultat = $baza -> SelectDB($upit);

        if($red = mysqli_fetch_assoc($rezultat)){
            echo json_encode($red);
        }
    }

    

    if(isset($_GET['update_cookiesAccept'])){
        session_start();
        if(isset($_SESSION['kor_id'])){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE korisnik SET uvjeti=1 WHERE korisnik_id = ?;";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("i", $_SESSION['kor_id']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode("Uspjeh");
                    zapisiULog($upit, '3', $veza, 'cookies accept'); 
                }
                else{
                    echo json_encode("Neuspjeh");
                }            
            }       
        }
    }

    if(isset($_GET['update_cookiesReset'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 3){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $baza -> spojiDB();
            $rezultat = $baza -> ostaliUpitDB("UPDATE korisnik SET uvjeti=0;");
            echo json_encode($rezultat); 
        }
    }

    if(isset($_GET['update_postavkeSet'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 3){
            $kljuc = array_key_first($_POST);
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE postavke SET ".$kljuc." = ?;";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("i", $_POST[$kljuc]);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode("Uspjeh");
                    zapisiULog($upit, '2', $veza, 'postavke set'); 
                }
                else{
                    echo json_encode("Neuspjeh");
                }            
            }      
        }
    }

    if(isset($_GET['fetch_dnevnikRada'])){
        if(!isset($_SESSION)){
            session_start();
        }
        if($_SESSION['uloga'] == 3){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit ="SELECT t2.ime, t3.naziv, t1.radnja, t1.upit FROM dnevnik AS t1 LEFT JOIN (SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime FROM korisnik) AS t2 ON t1.id_korisnik=t2.korisnik_id LEFT JOIN tip AS t3 ON t1.id_tip = t3.tip_id WHERE t1.vrijeme >= ? AND t1.vrijeme <= ?";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("ss", $_POST['od'], $_POST['do']);
                $stmt -> execute();
    
                $rezultat = $stmt->get_result();
                if(!$rezultat){
                    echo json_encode($stmt->errno);
                    die;
                }
                
                $redovi = [];
                if($rezultat -> num_rows > 0){
                    while ($red = $rezultat->fetch_assoc())
                    {
                        $redovi[] = $red;
                    }
                    echo json_encode($redovi);
                    zapisiULog($upit, '3', $veza, 'dnevnik rada u razdoblju');
                } 
            }   
        }
    }

    function zapisiULog($upit, $tip, $veza, $info){
        if(!isset($_SESSION)){
            session_start();
        }
        switch($upit[0]){
            case 'S': 
                $radnja = 'select '.$info;
                $stmt = $veza -> prepare ("INSERT INTO dnevnik (id_korisnik, id_tip, radnja, upit) VALUES (?,?,?,?);");
                $stmt -> bind_param("iiss", $_SESSION['kor_id'], $tip, $radnja, $upit);
                $stmt -> execute();
                
            break;
            case 'I': 
                $radnja = 'insert '.$info;
                $stmt = $veza -> prepare ("INSERT INTO dnevnik (id_korisnik, id_tip, radnja, upit) VALUES (?,?,?,?);");
                $stmt -> bind_param("iiss", $_SESSION['kor_id'], $tip, $radnja, $upit);
                $stmt -> execute();
        
            break;
            case 'U': 
                $radnja = 'update '.$info;
                $stmt = $veza -> prepare ("INSERT INTO dnevnik (id_korisnik, id_tip, radnja, upit) VALUES (?,?,?,?);");
                $stmt -> bind_param("iiss", $_SESSION['kor_id'], $tip, $radnja, $upit);
                $stmt -> execute();
            break;
        }
    }

    if(isset($_GET['passReset'])){
        if(isset($_POST['email']) && preg_match("/^\S+@\S+\.\S+$/", "ivuk@foi.hr")==true){
            $lozinka = strtoupper(dechex(random_int(0, 16777215)));
                $to = $_POST['email'];
                $subject = 'POŠTE Aktivacijski link';
                $message = '
                <html>
                <head>
                    <title>Aktivacijski link</title>
                    <style>
                    body{
                        font-family: Nunito, Roboto, Segoe UI;
                        font-size: 22px;
                        width: 100%;
                        height: 500px;
                        background-color: #707070;
                        overflow: hidden;
                    }

                    .wrapper{
                        position: absolute;
                        left: 50%;
                        top: 0;
                        transform: translateX(-50%);
                        background-color: #FFF;
                        height: 100%;
                        width: 600px;
                    }

                    main{
                        width: 80%;
                        position: absolute;
                        top: 70px;
                        left: 50%;
                        transform: translateX(-50%);
                        padding: 15px;
                    }
                    
                    .header{
                        width: 600px;
                        height: 85px;
                        border: solid 1px #707070;
                        background-color: #232323;
                    }

                    .headerText{
                        position: relative;
                        bottom: 7px;
                        font-size: 30px;
                        color: #f1cd7b;
                        display: block;
                        text-align: center;
                    }

                    .headerFigure{
                        display: block;
                        margin: auto;
                        width: 83px;
                        object-fit: contain;
                        position: relative;
                        top: 5px;
                    }

                    .headerImage{
                        width: 100%;
                    }

                    .boldedText{
                        font-weight: bold;
                    }
                    
                    a{
                        text-decoration: none;
                        color: green;
                    }
                    
                    a:hover{
                        text-decoration: underline;
                        cursor: pointer;
                    }
                    
                    .codeWrapper{
                      position: absolute;
                      left: 50%;
                      transform: translateX(-50%);
                    }
                    
                    .digit{
                      display: block;
                      height: 45px;
                      width: 240px;
                      background-color: #AFAFAF;
                      border: 1px solid #707070;
                      border-radius: 4px;
                      font-size: 40px;
                      line-height: 45px;
                      text-align: center;
                      vertical-align: middle;
                    }
                    
                    .digit:last-child{
                      margin-right: 0;
                    }

                    </style>
                </head>
                <body>
                    <div class="wrapper">
                    <header class="header">
                    <figure class="headerFigure"><a href="index.php"><img src="http://barka.foi.hr/WebDiP/2019_projekti/WebDiP2019x144/multimedija/post-icon.png" class="headerImage"></a></figure>
                    <span class="headerText">POŠTE</span>
                    </header>
                    <main>
                    <p class="boldedText">Poštovani '.$_POST['email'].', </p>
                    <p>
                        Nedavno ste zatražili ponovno postavljanje lozinke.
                    </p>
                    <p>
                        Vaša privremena šifra je: 
                    </p>
                    
                    <div class="codeWrapper">
                      <span class="digit">'.($lozinka).'</span>
                    </div>
                   
                    </main>
                    </div>

                    </body>
                </html>
                ';
                        
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                $headers[] = 'From: Poste@foi.hr' . "\r\n" .
                    'Reply-To: Poste@foi.hr' . "\r\n";

                
                mail($to, $subject, $message, implode("\r\n", $headers));

            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit ="UPDATE korisnik SET id_status='4', codeZaReset = ? WHERE email = ?";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo json_encode("Neuspjeh");
            }
            else{
                $stmt -> bind_param("ss", $lozinka, $_POST['email']);
                if(!$stmt -> execute()){
                    echo json_encode($stmt->errno);
                }
                echo json_encode($lozinka);
            }
        }
        else{
            echo json_encode("Neuspjeh");
        }
    }

    if(isset($_GET['setPassword'])){
        if(isset($_POST['lozinka']) && isset($_POST['email']) && isset($_POST['code'])){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit ="SELECT codeZaReset FROM korisnik WHERE codeZaReset=? AND id_status='4' AND email = ?";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo json_encode('Neuspjeh');
            }
            else{
                $stmt -> bind_param("ss", $_POST['code'], $_POST['email']);
                $stmt -> execute();
                
                $rezultat = $stmt->get_result();
                if($rezultat->num_rows == 0){
                    echo json_encode("Taj korisnik nije pronađen");
                    echo json_encode($_POST['code'],$_POSt['email']);
                    die;
                }
                else if($rezultat->num_rows > 0){
                    $upit ="UPDATE korisnik SET codeZaReset=NULL, id_status='2', lozinka=?, lozinka_sha1=? WHERE email = ?";
                    $stmt = $veza -> prepare ($upit);
                    if($stmt == null){
                        echo json_encode('Neuspjeh');
                    }
                    $lozinka_sha1 = sha1($_POST['lozinka']);
                    $stmt -> bind_param("sss", $_POST['lozinka'], $lozinka_sha1, $_POST['email']);
                    $stmt -> execute();
                    $rezultat = $stmt->get_result();
                    if($stmt->affected_rows == 0){
                        echo json_encode("Neuspjeh");
                    }
                    else{
                        echo json_encode("Uspjeh");
                    }
                }
            }
        }
        else{
            echo json_encode('Nisu svi podatci postavljeni!');
        }
    }

    if(isset($_GET['spremiPomak'])){
        session_start();
        //
        if(isset($_POST['brojSati']) && strlen($_POST['brojSati']) ){
            require_once("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE postavke SET pomakVremena = ?;";
            $stmt = $veza -> prepare($upit);
            if($stmt == null){
                echo mysqli_error($veza);
            }
            else{
                $stmt -> bind_param("i", $_POST['brojSati']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo json_encode("Uspjeh");
                    zapisiULog($upit, '2', $veza,'poštanski ured');
                }
                else{
                    echo json_encode("Neuspjeh");
                }
            }
        }
        else{
            echo 'notokej';
        }
    }

    if(isset($_GET['dohvatiFont'])){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT font FROM postavke;";
        $rezultat = $baza -> SelectDB($upit);
        if($red = mysqli_fetch_assoc($rezultat)){
           echo json_encode($red['font']);
        }
    }

    function dohvatiPomak(){
        require_once("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $rezultat = $baza -> SelectDB("SELECT pomakVremena FROM postavke;");
        
        if($red = mysqli_fetch_assoc($rezultat)){
            return $red['pomakVremena'];
        }
    }
?>