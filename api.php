<?php
    if(isset($_GET['fetch_postanskiUred']) && $_GET['fetch_postanskiUred']!=null){
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


?>
