<?php
    if(isset($_GET['fetch_postanskiUred'])){
        require("baza.class.php"); 
        $baza = new Baza;
        $baza -> spojiDB();
        $upit = "SELECT * FROM postanskiured;";
        $rezultat2 = $baza -> SelectDB($upit);
        echo '<tbody>';
        while($red = mysqli_fetch_assoc($rezultat2)){
            echo '
                <tr> 
                    <td>'.$red['naziv'].'</td>
                    <td>'.$red['adresa'].'</td>
                    <td>'.$red['postanskiBroj'].'</td>
                    <td style="display:none">'.$red['id_drzave'].'</td>
                </tr>
            ';
        }
        echo '</tbody>';
    }

    if(isset($_GET['insert_drzava'])){
        /*echo $_POST['naziv']. " ".$_POST['skraceniOblik']. " ".$_POST['produzeniOblik']. " ".$_POST['clanEU'];
        */
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

?>
