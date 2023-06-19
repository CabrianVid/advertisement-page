<?php
	session_start();

	//Seja poteče po 30 minutah - avtomatsko odjavi neaktivnega uporabnika
	if(isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] < 1800){
		session_regenerate_id(true);
	}
	$_SESSION['LAST_ACTIVITY'] = time();

	//Poveži se z bazo
	$conn = new mysqli('localhost', 'root', '20vid02A.', 'vaja1', 3308);
	//Nastavi kodiranje znakov, ki se uporablja pri komunikaciji z bazo
	$conn->set_charset("UTF8");
?>
<html>
<head>
    <title>Vaja 1</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1 class="text-center">Oglasnik</h1>
    <nav>
        <ul class="nav nav-pills">
            <li class="active"><a href="index.php">Domov</a></li>
            <?php
            if(isset($_SESSION["USER_ID"])){
                //check if user is admin
                $sql = "SELECT * FROM users WHERE id = " . $_SESSION["USER_ID"] AND "administrator = 1";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    if ($row["administrator"] == 1) {
                        ?>
                        <li><a href="publish.php">Objavi oglas</a></li>
                        <li><a href="admin.php">Admin</a></li>
                        <li><a href="logout.php">Odjava</a></li>
                        <li><a href="myAds.php">Profil</a></li>
                        <?php
                    } else {
                        ?>
                        <li><a href="publish.php">Objavi oglas</a></li>
                        <li><a href="logout.php">Odjava</a></li>
                        <li><a href="myAds.php">Profil</a></li>
                        <?php
                    }
            } else{
                ?>
                <li><a href="login.php">Prijava</a></li>
                <li><a href="register.php">Registracija</a></li>
                <?php
            }
            ?>
        </ul>
    </nav>
    <hr/>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
