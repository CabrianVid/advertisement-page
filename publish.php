<?php
include_once('header.php');


// Funkcija vstavi nov oglas v bazo. Preveri tudi, ali so podatki pravilno izpolnjeni. 
// Vrne false, če je prišlo do napake oz. true, če je oglas bil uspešno vstavljen.
function publish($title, $desc, $img, $categories){
	global $conn;
//
//    $sql = "SELECT * FROM categories";
//    $result = $conn->query($sql);
	$title = mysqli_real_escape_string($conn, $title);
	$desc = mysqli_real_escape_string($conn, $desc);
	$user_id = $_SESSION["USER_ID"];

	//Preberemo vsebino (byte array) slike
	$img_file = file_get_contents($img["tmp_name"]);
	//Pripravimo byte array za pisanje v bazo (v polje tipa LONGBLOB)
	$img_file = mysqli_real_escape_string($conn, $img_file);
	
	$query = "INSERT INTO ads (title, description, user_id, image, published_date)
				VALUES('$title', '$desc', '$user_id', '$img_file', NOW());";//shranimo podatke v bazo

    $temp=$conn->query($query);
    $ad_id = $conn->insert_id;

        foreach($categories as $category_id) {
            $sql = "INSERT INTO ad_categories (ad_id, category_id) VALUES ('$ad_id', '$category_id')";
            $conn->query($sql);
        }


    if($temp){
		return true;
	}
	else{
		//Izpis MYSQL napake z: echo mysqli_error($conn); 
		return false;
	}
	
	/*to ni treba
	//Pravilneje bi bilo, da sliko shranimo na disk. Poskrbeti moramo, da so imena slik enolična. V bazo shranimo pot do slike.
	//Paziti moramo tudi na varnost: v mapi slik se ne smejo izvajati nobene scripte (če bi uporabnik naložil PHP kodo). Potrebno je urediti ustrezna dovoljenja (permissions).
		
		$imeSlike=$photo["name"]; //Pazimo, da je enolično!
		//sliko premaknemo iz začasne poti, v neko našo mapo, zaradi preglednosti
		move_uploaded_file($photo["tmp_name"], "slika/".$imeSlike);
		$pot="slika/".$imeSlike;		
		//V bazo shranimo $pot
	*/
}
function get_categories()
{
    global $conn;
    $query = "SELECT * FROM categories;";
    $res = $conn->query($query);
    $categories = array();
    while ($category = $res->fetch_object()) {
        array_push($categories, $category);
    }
    return $categories;
}

global $conn;

$categories = get_categories();

$error = "";
if(isset($_POST["submit"])){
	if(publish($_POST["title"], $_POST["description"], $_FILES["image"], $_POST["categories"])){

		header("Location: index.php");
		die();
	}
	else{
		$error = "Prišlo je do napake pri objavi oglasa.";
	}
}

?>
    <h2 class="mb-4">Objavi oglas</h2>
    <form action="publish.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Naslov</label>
            <input type="text" name="title" class="form-control" id="title" />
        </div>
        <div class="form-group">
            <label for="description">Vsebina</label>
            <textarea name="description" rows="10" cols="50" class="form-control" id="description"></textarea>
        </div>
        <div class="form-group">
            <label for="image">Slika</label>
            <input type="file" name="image" class="form-control-file" id="image" />
        </div>
        <div class="form-group">
            <label>Kategorije:</label>
            <br>
            <?php
            foreach ($categories as $category) {
                ?>
                <span class="m-2">
                <input class="form-check-input" type="checkbox" value="<?php echo $category->id; ?>"
                       name="categories[]">
                    <label class="form-check-label"><?php echo $category->name; ?></label>
                </span>
                <?php
            }
            ?>
        </div>
        <input type="submit" name="submit" value="Objavi" class="btn btn-primary mb-3" />
        <br/>
        <label class="text-danger"><?php echo $error; ?></label>
    </form>
<?php
include_once('footer.php');
?>