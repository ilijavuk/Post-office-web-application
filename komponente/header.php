<header class="header">
    <span id="navButton" class="navButton">≡</span>
    <figure class="headerFigure"><a href="index.php"><img src="multimedija/post-icon.png" class="headerImage"></a></figure>
    <span class="headerText">POŠTE</span>
    <?php 
    if(!isset($_SESSION)){ 
        session_start(); 
    } 
    if(!isset($_SESSION['kor_id'])){
        echo '
        <button onclick="location.href=`./register.php`" class="button" style="grid-column: 4 / span 1;">Register</button>
        <button onclick="location.href=`./login.php`" class="button" style="grid-column: 6 / span 1;">Login</button>
        ';
    }
    else{
        echo '
        <button onclick="location.href=`./api.php?logout`" class="button" style="grid-column: 6 / span 1;">Logout</button>
        ';
    }
    ?>
</header>