<?php
include_once('header.php');

// Funkcija preveri, ali v bazi obstaja uporabnik z določenim imenom in vrne true, če obstaja.
function username_exists($username){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username); //moremo ga precistit zato da ne more kdo kode spreminjat
	$query = "SELECT * FROM users WHERE username='$username'";
	$res = $conn->query($query);
	return mysqli_num_rows($res) > 0;
}

// Funkcija ustvari uporabnika v tabeli users. Poskrbi tudi za ustrezno šifriranje uporabniškega gesla.
function register_user($username, $password, $firstName,$lastName, $email, $address, $post, $phoneNumber){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$pass = sha1($password);//sha1 ga zasifrira... boljse metode
	/* 
		Tukaj za hashiranje gesla uporabljamo sha1 funkcijo. V praksi se priporočajo naprednejše metode, ki k geslu dodajo naključne znake (salt).
		Več informacij: 
		http://php.net/manual/en/faq.passwords.php#faq.passwords 
		https://crackstation.net/hashing-security.htm
	*/
	$query = "INSERT INTO users (username, password, firstName, lastName, email, address, post, phoneNumber) VALUES ('$username', '$pass', '$firstName', '$lastName', '$email', '$address', '$post', '$phoneNumber');";
	if($conn->query($query)){
		return true;
	}
	else{
		echo mysqli_error($conn);
		return false;
	}
}

$error = "";
if(isset($_POST["submit"])){// ce se ni bil submitan se to seveda ne izvede
	/*
		VALIDACIJA: preveriti moramo, ali je uporabnik pravilno vnesel podatke (unikatno uporabniško ime, dolžina gesla,...)
		Validacijo vnesenih podatkov VEDNO izvajamo na strežniški strani. Validacija, ki se izvede na strani odjemalca (recimo Javascript), 
		služi za bolj prijazne uporabniške vmesnike, saj uporabnika sproti obvešča o napakah. Validacija na strani odjemalca ne zagotavlja
		nobene varnosti, saj jo lahko uporabnik enostavno zaobide (developer tools,...).
	*/
	//Preveri če se gesli ujemata
	if($_POST["password"] != $_POST["repeatPassword"]){
		$error = "Gesli se ne ujemata.";
	}
	//Preveri ali uporabniško ime obstaja
	else if(username_exists($_POST["username"])){
		$error = "Uporabniško ime je že zasedeno.";
	}
	//Podatki so pravilno izpolnjeni, registriraj uporabnika
	else if(register_user($_POST["username"], $_POST["password"], $_POST["firstName"], $_POST["lastName"], $_POST["email"], $_POST["address"], $_POST["post"], $_POST["phoneNumber"] )){
		header("Location: login.php");
		die();
	}
	//Prišlo je do napake pri registraciji
	else{
		$error = "Prišlo je do napake med registracijo uporabnika.";
	}
}

?><div class="container">
    <h2 class="text-center">Registracija</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Uporabniško ime:</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Geslo:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="form-group">
                    <label for="repeatPassword">Ponovi geslo:</label>
                    <input type="password" class="form-control" name="repeatPassword" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label for="firstName">First name:</label>
                    <input type="text" class="form-control" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last name:</label>
                    <input type="text" class="form-control" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" class="form-control" name="address" required>
                </div>
                <div class="form-group">
                    <label for="post">Post:</label>
                    <input type="text" class="form-control" name="post" required>
                </div>
                <div class="form-group">
                    <label for="phoneNumber">Phone number:</label>
                    <input type="text" class="form-control" name="phoneNumber" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="Pošlji" class="btn btn-primary"/>
                </div>
                <label><?php echo $error; ?></label>
            </form>
        </div>
    </div>
    </div>
<?php
include_once('footer.php');
?>