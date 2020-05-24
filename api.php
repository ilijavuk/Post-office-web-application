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
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $stmt = $veza -> prepare("INSERT INTO postanskiured (naziv, skraceniOblik, produzeniOblik, clanEU) VALUES (?, ?, ?, ?);");
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("sssi", $_POST['naziv'], $_POST['skraceniOblik'], $_POST['produzeniOblik'], $_POST['clanEU']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
            }
            else{
                echo "Neuspjeh";
            }
        }
    }

    if(isset($_GET['insert_drzava'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $stmt = $veza -> prepare("INSERT INTO drzava (naziv, skraceniOblik, produzeniOblik, clanEU) VALUES (?, ?, ?, ?);");
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("sssi", $_POST['naziv'], $_POST['skraceniOblik'], $_POST['produzeniOblik'], $_POST['clanEU']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare ("SELECT t1.naziv, COUNT(t3.posiljka_id) as broj_posiljki, SUM(case t4.placen when '1' then 1 else 0 end) AS broj_placenih FROM drzava AS t1 LEFT JOIN postanskiured AS t2 ON t1.drzava_id=t2.id_drzave LEFT JOIN posiljka AS t3 ON t2.postanskiUred_id=t3.id_pocetniUred LEFT JOIN racun AS t4 ON t3.posiljka_id=t4.id_posiljka WHERE t3.vrijeme_slanja >= ? AND t3.vrijeme_slanja <= ? AND t2.id_moderatora = ? GROUP BY t1.naziv;");
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
        }        
    }

    if(isset($_GET['insert_racun'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $stmt = $veza->prepare("SELECT t1.id_moderatora FROM postanskiured as t1 LEFT JOIN posiljka as t2 ON t1.postanskiUred_id=t2.id_konacniUred WHERE posiljka_id = ?");
        if($stmt == null){
            echo 'Neuspjeh dohvaćanje id-a moderatora';
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

               $stmt = $veza -> prepare("INSERT INTO racun (id_posiljka, id_moderatora, vrijemeIzdavanja, rokZaPlacanje, placen, iznos) VALUES (?, ?, ?, ?, '0', ?);");
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
        $stmt = $veza -> prepare("UPDATE racun SET placen='1', slika=? WHERE racun_id = ?;");
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("ss", $_POST['slika'], $_POST['racun_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare("UPDATE racun SET puniIznos=(iznos+?) WHERE racun_id = ?;");
        if($stmt == null){
            echo 'Neuspjeh 1';
        }
        else{
            $stmt -> bind_param("ss", $_POST['iznos_obrade'], $_POST['racun_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare ("SELECT korisnik_id, CONCAT(ime,' ',prezime,'(',korisnicko_ime,')') AS ime FROM korisnik AS t1 LEFT JOIN posiljka AS t2 ON t1.korisnik_id=t2.id_primatelja LEFT JOIN racun AS t3 ON t2.posiljka_id=t3.id_posiljka WHERE racun_id = ?;");
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

            if ($red = $rezultat->fetch_assoc())
            {
                echo  '<tr>
                    <td>'.$red['korisnik_id'].'</td>
                    <td>'.$red['ime'].'</td>
                </tr>';
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
        $stmt = $veza -> prepare("UPDATE korisnik SET blokiranDo=?, id_status=3 WHERE korisnik_id = ?;");
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("si", $datum, $_POST['korisnik_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare("UPDATE korisnik SET blokiranDo=NULL, id_status=2, neuspjeliLogin=0 WHERE korisnik_id = ?;");
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("i", $_POST['korisnik_id']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare("INSERT INTO posiljka (id_posiljatelja, id_primatelja, masa) VALUES (?, ?, ?);");
        if($stmt == null){
            echo 'Neuspjeh $stmt = null';
        }
        else{
            $stmt -> bind_param("iii", $_SESSION['kor_id'], $_POST['id_primatelja'], $_POST['masa']);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare("UPDATE posiljka SET id_pocetniUred=?, id_konacniUred=?, id_trenutniUred=?, cijenaPoKg=? WHERE posiljka_id=?;");
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
            $stmt = $veza -> prepare("UPDATE posiljka SET spremnaZaIsporuku=1 WHERE posiljka_id=?;");
        }
        else if($_GET['update_posiljkaProslijedi'] == 2){
            $stmt = $veza -> prepare("UPDATE posiljka SET id_trenutniUred=? WHERE posiljka_id=?;");
        
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
        $stmt = $veza -> prepare ("SELECT * FROM korisnik WHERE korisnicko_ime = ?;");
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
        $stmt = $veza -> prepare("INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka, lozinka_sha1, email, linkAktivacije) VALUES (?, ?, ?, ?, ?, ?, ?);");
        if($stmt == null){
            echo 'Neuspjeh $stmt = null';
        }
        else{
            $stmt -> bind_param("sssssss", $_POST['ime'], $_POST['prezime'], $_POST['korisnicko_ime'], $_POST['lozinka'], $lozinka_sha1, $_POST['email'], $link_aktivacije);
            $stmt -> execute();
            if($stmt->affected_rows > 0){
                echo "Uspjeh";
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
        $stmt = $veza -> prepare ("SELECT * FROM korisnik WHERE korisnicko_ime = ?;");
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
            $stmt = $veza -> prepare ("SELECT korisnik_id, id_uloga, id_status, blokiranDo, neuspjeliLogin FROM korisnik WHERE korisnicko_ime = ? AND lozinka_sha1 = ?;");
            $stmt -> bind_param("ss", $_POST['korisnicko_ime'], $lozinka_sha1);
        }
        else if(isset($_POST['email']))
        {
            $stmt = $veza -> prepare ("SELECT korisnik_id, id_uloga, id_status, blokiranDo, neuspjeliLogin FROM korisnik WHERE email = ? AND lozinka_sha1 = ?;");
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
    
                if(isset($_POST['korisnicko_ime'])){
                    $stmt = $veza -> prepare ("UPDATE korisnik SET neuspjeliLogin = 0 WHERE korisnicko_ime = ?");
                    $stmt -> bind_param("s", $_POST['korisnicko_ime']);
                }
                else if(isset($_POST['email'])){
                    $stmt = $veza -> prepare ("UPDATE korisnik SET neuspjeliLogin = 0 WHERE email = ?");
                    $stmt -> bind_param("s", $_POST['email']);
                }
                $stmt -> execute();
                echo  '1';
                $_SESSION['LAST_ACTIVITY'] = time(); 
            }
        }
        else{
            if(isset($_POST['korisnicko_ime'])){
                $stmt = $veza -> prepare ("UPDATE korisnik SET neuspjeliLogin = neuspjeliLogin + 1 WHERE korisnicko_ime = ?");
                $stmt -> bind_param("s", $_POST['korisnicko_ime']);
            }
            else if(isset($_POST['email'])){
                $stmt = $veza -> prepare ("UPDATE korisnik SET neuspjeliLogin = neuspjeliLogin + 1 WHERE email = ?");
                $stmt -> bind_param("s", $_POST['email']);
            }
            $stmt -> execute();
            echo '0';
        }
    }

    if(isset($_GET['fetch_galerija'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $stmt = $veza -> prepare ("SELECT t2.slika FROM posiljka AS t1 RIGHT JOIN racun AS t2 ON t1.posiljka_id=t2.id_posiljka WHERE dopustenjeZaObjavu = 1 AND slika IS NOT NULL AND t1.id_konacniUred=?;");
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
        }   
    }

    if(isset($_GET['check_timeout'])){
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            // last request was more than 30 minutes ago
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
        }
    }

    if(isset($_GET['test'])){
        session_start();
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60)) {
            echo 'prošla je minuta';
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
        }
        echo time() - $_SESSION['LAST_ACTIVITY'];
    }

?>