<?php
    if(isset($_GET['fetch_postanskiUred'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT * FROM postanskiured AS t1 LEFT JOIN (SELECT id_pocetniUred, COUNT(*) AS 'broj_poslanih' FROM posiljka GROUP BY id_pocetniUred) AS t2 ON  t1.postanskiUred_id = t2.id_pocetniUred INNER JOIN (SELECT postanskiUred_id, t4.broj_primljenih FROM postanskiured AS t3 LEFT JOIN (SELECT id_konacniUred, COUNT(*) AS 'broj_primljenih' FROM posiljka GROUP BY id_konacniUred) AS t4 ON  t3.postanskiUred_id = t4.id_konacniUred) AS q2 ON q2.postanskiUred_id=t1.postanskiUred_id";
        $rezultat2 = $baza -> SelectDB($upit);
        echo '<tbody>';
        while($red = mysqli_fetch_assoc($rezultat2)){
            echo '
                <tr> 
                    <td>'.$red['naziv'].'</td>
                    <td>'.$red['adresa'].'</td>
                    <td>'.$red['postanskiBroj'].'</td>
                    <td style="display:none">'.$red['id_drzave'].'</td>
                    <td>'.$red['broj_poslanih'].'</td>
                    <td>'.$red['broj_primljenih'].'</td>
                </tr>
            ';
        }
        echo '</tbody>';
    }

    if(isset($_GET['insert_postanskiUred'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 3){
            require("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "INSERT INTO postanskiured (id_moderatora, id_drzave, naziv, adresa, postanskiBroj) VALUES (?, ?, ?, ?, ?);";
            $stmt = $veza -> prepare($upit);
            if($stmt == null){
                echo 'Neuspjeh';
            }
            else{
                $stmt -> bind_param("iisss", $_POST['id_moderatora'], $_POST['id_drzave'], $_POST['naziv'], $_POST['adresa'], $_POST['postanskiBroj']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo "Uspjeh";
                    zapisiULog($upit, '2', $veza,'poštanski ured');
                }
                else{
                    echo "Neuspjeh";
                }
            }
        }
    }

    if(isset($_GET['insert_drzava'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "INSERT INTO drzava (naziv, skraceniOblik, produzeniOblik, clanEU) VALUES (?, ?, ?, ?);";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("sssi", $_POST['naziv'], $_POST['skraceniOblik'], $_POST['produzeniOblik'], $_POST['clanEU']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'drzava');
            }
            else{
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['fetch_drzaveStatistika'])){
        require("baza.class.php");
        session_start();
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT t1.naziv, COUNT(t3.posiljka_id) as broj_posiljki, SUM(case t4.placen when '1' then 1 else 0 end) AS broj_placenih FROM drzava AS t1 LEFT JOIN postanskiured AS t2 ON t1.drzava_id=t2.id_drzave LEFT JOIN posiljka AS t3 ON t2.postanskiUred_id=t3.id_pocetniUred LEFT JOIN racun AS t4 ON t3.posiljka_id=t4.id_posiljka WHERE t3.vrijeme_slanja >= ? AND t3.vrijeme_slanja <= ? AND t2.id_moderatora = ? GROUP BY t1.naziv;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("ssi", $_POST['od'], $_POST['do'], $_SESSION['kor_id']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo $stmt->errno;
            }

            while ($red = $rezultat->fetch_assoc())
            {
                $broj_posiljki = ($red['broj_posiljki'] == '' ? 0 : $red['broj_posiljki']);
                echo  ' <tr>
                <td>'.$red['naziv'].'</td>
                <td>'.$broj_posiljki.'</td>
                <td>'.$red['broj_placenih'].'</td>
                </tr>';
            }
            zapisiULog($upit, '2', $veza, 'statistika drzave'); 
        }        
    }

    if(isset($_GET['insert_racun'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit1 = "SELECT t1.id_moderatora FROM postanskiured as t1 LEFT JOIN posiljka as t2 ON t1.postanskiUred_id=t2.id_konacniUred WHERE posiljka_id = ?";
        $stmt = $veza->prepare($upit1);
        if($stmt == null){
            echo 'Neuspje dohvaćanje id-a moderatora';
        }
        else{
            $stmt -> bind_param("i", $_POST['id_posiljka']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo $stmt->errno;
            }

            if ($red = $rezultat->fetch_assoc())
            {
                $id_moderatora = $red['id_moderatora'];
                $upit2 = "INSERT INTO racun (id_posiljka, id_moderatora, vrijemeIzdavanja, rokZaPlacanje, placen, iznos) VALUES (?, ?, ?, ?, '0', ?);";
                $stmt = $veza -> prepare($upit2);
                if($stmt == null){
                   echo 'Neuspjeh $stmt = null';
                   }
                   else{
                       $currTime = date("Y-m-d")." ".date("H:i:s");
                       $rokZaPlacanje = date("Y-m-d", strtotime("+7 days"))." ".date("H:i:s");
                       $stmt -> bind_param("iissi", $_POST['id_posiljka'], $id_moderatora, $currTime, $rokZaPlacanje, $_POST['iznos']);
                       $stmt -> execute();
                       if($stmt->affected_rows > 0){
                            echo "Uspjeh";
                            zapisiULog($upit1, '2', $veza, 'račun');  
                            zapisiULog($upit2, '2', $veza, 'račun');                        
                    }
                    else{
                       die('execute() failed: ' . htmlspecialchars($stmt->error));
                       echo "Neuspjeh";
                    }
                }
            }
            else{
                echo '0 rows fetched';
            }
        }       
    }

    if(isset($_GET['update_racun'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE racun SET placen='1', slika=? WHERE racun_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("ss", $_POST['slika'], $_POST['racun_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'račun');  
            }
            else{
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['update_racunDodajIznos'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE racun SET puniIznos=(iznos+?) WHERE racun_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh 1';
        }
        else{
            $stmt -> bind_param("ss", $_POST['iznos_obrade'], $_POST['racun_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'račun dodaj iznos');  
            }
            else{
                echo "Neuspje 2h";
            }
        }
    }

    if(isset($_GET['fetch_role'])){
        session_start();
        echo $_SESSION['uloga'];
    }

    if(isset($_GET['fetch_korisnikFromRacun'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime FROM korisnik AS t1 LEFT JOIN posiljka AS t2 ON t1.korisnik_id=t2.id_primatelja LEFT JOIN racun AS t3 ON t2.posiljka_id=t3.id_posiljka WHERE racun_id = ?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("i", $_POST['racun_id']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo $stmt->errno;
            }
            
            if ($red = $rezultat->fetch_assoc()){
                echo  '<tr>
                    <td>'.$red['korisnik_id'].'</td>
                    <td>'.$red['ime'].'</td>
                </tr>';
                zapisiULog($upit, '2', $veza, 'korisnik from račun');  
            }
            else{
                echo '0 rows fetched';
            }
        }       
    }

    if(isset($_GET['update_korisnikBlock'])){
        $datum = date_create_from_format('D M d Y H:i:s e+', $_POST['blokiranDo']);
        $datum = $datum->format('Y-m-d H:i:s');
        
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE korisnik SET blokiranDo=?, id_status=3 WHERE korisnik_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("si", $datum, $_POST['korisnik_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'korisnik block');  
            }
            else{
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['update_korisnikUnblock'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE korisnik SET blokiranDo=NULL, id_status=2, neuspjeliLogin=0 WHERE korisnik_id = ?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("i", $_POST['korisnik_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'korisnik unblock');
            }
            else{
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['insert_posiljka'])){
        session_start();
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "INSERT INTO posiljka (id_posiljatelja, id_primatelja, masa) VALUES (?, ?, ?);";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh $stmt = null';
        }
        else{
            $stmt -> bind_param("iii", $_SESSION['kor_id'], $_POST['id_primatelja'], $_POST['masa']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'pošiljka');  
            }
            else{
                die('execute() failed: ' . htmlspecialchars($stmt->error));
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['update_posiljkaAktiviraj'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "UPDATE posiljka SET id_pocetniUred=?, id_konacniUred=?, id_trenutniUred=?, cijenaPoKg=? WHERE posiljka_id=?;";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("iiisi", $_POST['id_pocetniUred'], $_POST['id_konacniUred'], $_POST['id_pocetniUred'], $_POST['cijenaPoKg'], $_POST['posiljka_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";                
            }
            else{
                echo ($_POST['id_pocetniUred']);
                echo ($_POST['id_konacniUred']);
                echo ($_POST['cijenaPoKg']);
                echo ($_POST['posiljka_id']);
                echo "Neuspjeh didnt get post";
            }
        }
    }

    if(isset($_GET['update_posiljkaProslijedi'])){
        require("baza.class.php"); 
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
            echo 'Neuspjeh';
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
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'pošiljka proslijedi');  
            }
            else{
                echo "Neuspjeh didnt get post";
            }
        }
    }

    if(isset($_GET['fetch_korisnickoIme'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT * FROM korisnik WHERE korisnicko_ime = ?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("s", $_POST['korisnicko_ime']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo $stmt->errno;
            }

            if ($red = $rezultat->fetch_assoc())
            {
                echo  '1';
                zapisiULog($upit, '2', $veza, ' korisnik');  
            }
            else{
                echo '0';
            }
        }       
    }
    if(isset($_GET['insert_korisnik'])){
        $lozinka_sha1 = sha1($_POST['lozinka']);
        $link_aktivacije = sha1($_POST['korisnicko_ime']);

        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka, lozinka_sha1, email, linkAktivacije) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $stmt = $veza -> prepare($upit);
        if($stmt == null){
            echo 'Neuspjeh $stmt = null';
        }
        else{
            $stmt -> bind_param("sssssss", $_POST['ime'], $_POST['prezime'], $_POST['korisnicko_ime'], $_POST['lozinka'], $lozinka_sha1, $_POST['email'], $link_aktivacije);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
                zapisiULog($upit, '2', $veza, 'korisnik');  
            }
            else{
                die('execute() failed: ' . htmlspecialchars($stmt->error));
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['update_korisnikActivate'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT * FROM korisnik WHERE korisnicko_ime = ?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("s", $_POST['korisnicko_ime']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo $stmt->errno;
            }

            if ($red = $rezultat->fetch_assoc())
            {
                echo  '1';
                zapisiULog($upit, '2', $veza, 'korisnik activate');  
            }
            else{
                echo '0';
            }
        }       
    }

    if(isset($_GET['login'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $lozinka_sha1 = sha1($_POST['lozinka']);
        session_start();

        if(isset($_POST['korisnicko_ime'])){
            $upit1 = "SELECT uvjeti, korisnik_id, id_uloga, id_status, blokiranDo, neuspjeliLogin FROM korisnik WHERE korisnicko_ime = ? AND lozinka_sha1 = ?;";
            $stmt = $veza -> prepare ($upit1) or trigger_error($veza->error, E_USER_ERROR);
            $stmt -> bind_param("ss", $_POST['korisnicko_ime'], $lozinka_sha1);
        }
        else if(isset($_POST['email']))
        {
            $upit1 = "SELECT uvjeti, korisnik_id, id_uloga, id_status, blokiranDo, neuspjeliLogin FROM korisnik WHERE email = ? AND lozinka_sha1 = ?;";
            $stmt = $veza -> prepare ($upit1);
            $stmt -> bind_param("ss", $_POST['email'], $lozinka_sha1);
        }

        $stmt -> execute();

        $rezultat = $stmt->get_result();
        if(!$rezultat){
            echo $stmt->errno;
        }

        if ($red = $rezultat->fetch_assoc())
        {
            if($red['neuspjeliLogin'] >= 3){
                echo 'Zaključani ste!';
            }
            else{
                $_SESSION['kor_id'] = $red['korisnik_id'];
                $_SESSION['uloga'] = $red['id_uloga'];
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
                echo  '1';
                $_SESSION['LAST_LOGIN'] = time(); 
            }
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
            echo '0';
        }
        zapisiULog($upit1, '1', $veza, 'login');  
        zapisiULog($upit2, '1', $veza, 'login');  
    }

    if(isset($_GET['logout'])){
        session_start();
        session_unset();
        session_destroy(); 
        header('Location: login.php'); 

        $upit = "INSERT LOGOUT";

        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        zapisiULog($upit, '1', $veza, 'logout');    
    }

    if(isset($_GET['fetch_galerija'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $upit = "SELECT t2.slika FROM posiljka AS t1 RIGHT JOIN racun AS t2 ON t1.posiljka_id=t2.id_posiljka WHERE dopustenjeZaObjavu = 1 AND slika IS NOT NULL AND t1.id_konacniUred=?;";
        $stmt = $veza -> prepare ($upit);
        if($stmt == null){
                echo 'Neuspjeh';
            }
        else{
            $stmt -> bind_param("i", $_POST['postanskiUred_id']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();
            if(!$rezultat){
                echo $stmt->errno;
            }

            while ($red = $rezultat->fetch_assoc())
            {
                echo  $red['slika'].' ';
            }
            zapisiULog($upit, '2', $veza, 'galerija'); 
        }   
    }

    if(isset($_GET['check_timeout'])){
        session_start();
        require("baza.class.php"); 
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
        require("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT trajanjeKolacica FROM postavke;";
        $rezultat = $baza -> SelectDB($upit);

        if($red = mysqli_fetch_assoc($rezultat)){
            $trajanjeKolacica = 3600 * $red['trajanjeKolacica'];
            echo $trajanjeKolacica;
        }
    }

    if(isset($_GET['update_cookiesAccept'])){
        session_start();
        if(isset($_SESSION['kor_id'])){
            require("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE korisnik SET uvjeti=1 WHERE korisnik_id = ?;";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo 'Neuspjeh';
            }
            else{
                $stmt -> bind_param("i", $_SESSION['kor_id']);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo "Uspjeh";
                    zapisiULog($upit, '3', $veza, 'cookies accept'); 
                }
                else{
                    echo "Neuspjeh";
                }            
            }       
        }
    }

    if(isset($_GET['update_cookiesReset'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 3){
            require("baza.class.php"); 
            $baza = new Baza;
            $baza -> spojiDB();
            $rezultat = $baza -> ostaliUpitDB("UPDATE korisnik SET uvjeti=0;");
            header('Location: postavke.php');   
        }
    }

    if(isset($_GET['update_postavkeSet'])){
        session_start();
        if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 3){
            $kljuc = array_key_first($_POST);
            require("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit = "UPDATE postavke SET ".$kljuc." = ?;";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo $stmt->errno;
                echo 'Neuspjeh';
            }
            else{
                $stmt -> bind_param("i", $_POST[$kljuc]);
                $stmt -> execute();
                if($stmt->affected_rows > 0){
                    echo "Uspjeh";
                    zapisiULog($upit, '2', $veza, 'postavke set'); 
                }
                else{
                    die('execute() failed: ' . htmlspecialchars($stmt->error));
                    echo "Neuspjeh";
                }            
            }      
        }
    }

    if(isset($_GET['fetch_dnevnikRada'])){
        if(!isset($_SESSION)){
            session_start();
        }
        if($_SESSION['uloga'] == 3){
            require("baza.class.php"); 
            $baza = new Baza;
            $veza = $baza -> spojiDB();
            $upit ="SELECT t1.radnja, t1.upit, t2.ime, t3.naziv FROM dnevnik AS t1 LEFT JOIN (SELECT korisnik_id, CONCAT(ime,' ', prezime,'(',korisnicko_ime,')') AS ime FROM korisnik) AS t2 ON t1.id_korisnik=t2.korisnik_id LEFT JOIN tip AS t3 ON t1.id_tip = t3.tip_id WHERE t1.vrijeme >= ? AND t1.vrijeme <= ?";
            $stmt = $veza -> prepare ($upit);
            if($stmt == null){
                echo 'Neuspjeh';
            }
            else{
                echo $_POST['od']." ".$_POST['do'];
                $stmt -> bind_param("ss", $_POST['od'], $_POST['do']);
                $stmt -> execute();
    
                $rezultat = $stmt->get_result();
                if(!$rezultat){
                    echo $stmt->errno;
                }
                
                if($rezultat -> num_rows > 0){
                    while ($red = $rezultat->fetch_assoc())
                    {
                        echo '
                        <tr class="dnevnikRedak">
                            <td>'.$red['ime'].'</td>
                            <td>'.$red['naziv'].'</td>
                            <td>'.$red['radnja'].'</td>
                            <td style="display:none;">'.$red['upit'].'</td>
                        </tr>';
                    }
                    zapisiULog($upit, '3', $veza, 'dnevnik rada u razdoblju');
                } 
                else{
                    echo '
                        <tr>
                            <td colspan=3>U odabranom razdoblju ne postoje podatci</td> 
                        </tr>';
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

?>