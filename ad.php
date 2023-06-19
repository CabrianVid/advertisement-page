<?php
include_once('header.php');

//Funkcija izbere oglas s podanim ID-jem. Doda tudi uporabnika, ki je objavil oglas.
function get_ad($id){
	global $conn;
	$id = mysqli_real_escape_string($conn, $id);
	$query = "SELECT ads.*, users.username FROM ads LEFT JOIN users ON users.id = ads.user_id WHERE ads.id = $id;";
	$res = $conn->query($query);
    $result = $conn->query("SELECT * FROM ad_categories WHERE $id = ad_id");
//    // Fetch the results as an associative array
//    $categories = array();
//    while ($row = $result->fetch_assoc()) {
//        $categories[] = $row;
//    }
	if($obj = $res->fetch_object()){
		return $obj;
	}
	return null;
}

function getcategories($id){
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM ad_categories WHERE ad_id = '$id';";
    $res = $conn->query($query);
    $categories = array();
    while($obj = $res->fetch_object()){
        $categories[] = $obj;
    }
    return $categories;
}

function getComments($id){
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM comments WHERE ad_id = '$id';";
    $res = $conn->query($query);
    $comments = array();
    while($obj = $res->fetch_object()){
        $comments[] = $obj;
    }
    return $comments;
}

if(!isset($_GET["id"])){
	echo "Manjkajoči parametri.";
	die();
}

$id = $_GET["id"];
$ad = get_ad($id);
$categories = getcategories($id);
$comments = getComments($id);

if($ad == null){
	echo "Oglas ne obstaja.";
	die();
}
//
//function addComment($user_id, $ad_id, $comment){
//    global $conn;
//    $query = "INSERT INTO comments (text, user_id, ad_id)VALUES('$comment','$user_id', '$ad_id');";//shranimo podatke v bazo
//
//    if($conn->query($query)){
//        return true;
//    }
//    else{
//        echo mysqli_error($conn);
//        return false;
//    }
//
//}


//if(isset($_POST["submitComment"])){
////       $user_id = $_SESSION["USER_ID"];
//    if(addComment($_POST["user_id1"], $_POST["ad_id1"], $_POST["comment"])){
//        header("Location: index.php");
//        die();
//    }	else{
//        $error = "Prišlo je do napake pri objavi komentarja.";
//    }
//
//}



//Base64 koda za sliko (hexadecimalni zapis byte-ov iz datoteke)
$img_data = base64_encode($ad->image);
?><div class="container">
    <div class="card">
        <div class="card-header">
            <h4><?php echo $ad->title;?></h4>
        </div>
        <div class="card-body">
            <p class="card-text"><?php echo $ad->description;?></p>
            <p class="card-text">
                <?php
                global $conn;
                $result = $conn->query("SELECT * FROM ad_categories JOIN categories ON categories.id = ad_categories.category_id WHERE ad_id = '$ad->id'");
                // Fetch the results as an associative array
                $categories = array();
                while ($category = $result->fetch_object()) {?>
                    <?php echo $category->name; ?>
                <?php } ?>
            </p>
            <img src="data:image/jpg;base64, <?php echo $img_data;?>" class="card-img-top" alt="ad image">
            <p class="card-text">Objavil: <?php echo $ad->username; ?></p>
            <p class="card-text">Komentarji:</p>
            <p class="card-text">
                <?php
                global $conn;
                $result = $conn->query("SELECT * FROM comments WHERE ad_id = '$ad->id'");
                // Fetch the results as an associative array
                $comments = array();
                while ($comments = $result->fetch_object()) {?>
                    <?php echo $comments->text; ?><br>
                <?php } ?>
            </p>
<!--            <form action="index.php" method="POST" enctype="multipart/form-data">-->
<!--                <div class="form-group">-->
<!--                    <input type="hidden" name="user_id1" value="--><?php //echo $_SESSION["USER_ID"];?><!--">-->
<!--                    <input type="hidden" name="ad_id1" value="--><?php //echo $ad->id;?><!--">-->
<!---->
<!--                    <p class="card-text">user id: --><?php //echo $_SESSION["USER_ID"];?><!--</p>-->
<!--                    <p class="card-text">ad id: --><?php //echo $ad->id;?><!--</p>-->
<!---->
<!--                    <label for="title">Komentar</label>-->
<!--                    <input type="text" name="comment" class="form-control"/>-->
<!--                </div>-->
<!--                <input type="submit" name="submitComment" value="Objavi Komentar" class="btn btn-primary mb-3" />-->
<!---->
<!--            </form>-->
            <a href="index.php" class="btn btn-primary">Nazaj</a>
        </div>
    </div>
    </div>
    <hr/>
	<?php

include_once('footer.php');
?>