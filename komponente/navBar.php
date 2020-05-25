<nav class="navBar">
    <a href="posiljke.php" class="navLink">Pošiljke</a>
    <a href="racuni.php" class="navLink">Računi</a>
    <a href="uredi.php" class="navLink">Uredi</a>
    <a href="drzave.php" class="navLink">Države</a>
    <a href="o_autoru.php" class="navLink">O autoru</a>
    <a href="dokumentacija.php" class="navLink">Dokumentacija</a>
    <?php
    if(!isset($_SESSION)){ 
        session_start(); 
    } 
    if(!isset($_SESSION['kor_id'])){
        echo '
        <a href="register.php" class="navLink mobileOnly">Register</a>
        <a href="login.php" class="navLink mobileOnly">Login</a>
        ';
    }
    else if(isset($_SESSION['kor_id']) && $_SESSION['kor_id'] != null){
        echo '
        <a href="api.php?logout" class="navLink mobileOnly">Logout</a>
        ';
    }
    ?>
</nav>