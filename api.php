<?php
    if(isset($_GET['fetch_postanskiUred'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT * FROM postanskiured AS t1 LEFT JOIN (SELECT id_pocetniUred, COUNT(*) AS 'broj_posiljki' FROM posiljka GROUP BY id_pocetniUred) AS t2 ON  t1.postanskiUred_id = t2.id_pocetniUred;";
        $rezultat2 = $baza -> SelectDB($upit);
        echo '<tbody>';
        while($red = mysqli_fetch_assoc($rezultat2)){
            echo '
                <tr> 
                    <td>'.$red['naziv'].'</td>
                    <td>'.$red['adresa'].'</td>
                    <td>'.$red['postanskiBroj'].'</td>
                    <td style="display:none">'.$red['id_drzave'].'</td>
                    <td>'.$red['broj_posiljki'].'</td>
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
        $baza = new Baza;
        $veza = $baza -> spojiDB();
        $stmt = $veza -> prepare ("SELECT t1.naziv, COUNT(t3.posiljka_id) as broj_posiljki, SUM(case t4.placen when '1' then 1 else 0 end) AS broj_placenih FROM drzava AS t1 LEFT JOIN postanskiured AS t2 ON t1.drzava_id=t2.id_drzave LEFT JOIN posiljka AS t3 ON t2.postanskiUred_id=t3.id_pocetniUred LEFT JOIN racun AS t4 ON t3.posiljka_id=t4.id_posiljka WHERE t3.vrijeme_slanja >= ? AND t3.vrijeme_slanja<= ? GROUP BY t1.naziv;");
        if($stmt == null){
            echo 'Neuspjeh';
        }
        else{
            $stmt -> bind_param("ss", $_POST['od'], $_POST['do']);
            $stmt -> execute();

            $rezultat = $stmt->get_result();

            while ($red = $rezultat->fetch_assoc())
            {
                $broj_posiljki = ($red['broj_posiljki'] ==  '' ? 0 : $red['broj_posiljki']);
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
        $stmt = $veza -> prepare("INSERT INTO racun (id_posiljka, vrijemeIzdavanja, placen, iznos) VALUES (?, ?, '0', ?);");
        if($stmt == null){
            echo 'Neuspjeh $stmt = null';
        }
        else{
            $currTime = date("Y-m-d")." ".date("H:i:s");
            $stmt -> bind_param("isi", $_POST['id_posiljka'], $currTime, $_POST['iznos']);
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
        $stmt = $veza -> prepare("UPDATE korisnik SET blokiranDo=? WHERE korisnik_id = ?;");
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
?>