<?php
include_once('header.php');

// Funkcija prebere oglase iz baze in vrne polje objektov
function get_ads(){
    global $conn;// v headerju smo mogli spostavit povezavo s bazo
    //get user id
    $user_id = $_SESSION["USER_ID"];
    $query = "SELECT * FROM ads WHERE user_id = $user_id ORDER BY published_date DESC;";//zberamo vse oglase
    $res = $conn->query($query);
    $ads = array();
    while($ad = $res->fetch_object()){
        array_push($ads, $ad);
    }
    return $ads;
}

if(isset($_POST["change"])){
    //change ad
    if(change_ad($_POST["title"], $_POST["description"], $_FILES["image"], $_POST["id"])){
        header("Location: index.php");
        die();
    }	else{
        $error = "Prišlo je do napake pri spremembi  oglasa.";
    }

}

function change_ad($title, $description, $image, $ad_id){
    global $conn;
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $user_id = $_SESSION["USER_ID"];

    $img_file = file_get_contents($image["tmp_name"]);
    //Pripravimo byte array za pisanje v bazo (v polje tipa LONGBLOB)
    $img_file = mysqli_real_escape_string($conn, $img_file);



    $query = "UPDATE ads SET title = '$title', description = '$description', image = '$img_file' WHERE id ='$ad_id'";
    $res = $conn->query($query);
    if($res){
        return true;
    } else{
        return false;
    }
}

if(isset($_POST["delete"])){
    delete_ad($_POST["id"]);

}
function delete_ad($id){
    global $conn;
    $query = "DELETE FROM ads WHERE id = $id";
    $res = $conn->query($query);
}

if(isset($_POST["deleteComment"])){
    delete_comment($_POST["commentId"]);

}
function delete_comment($id){
    global $conn;
    $query = "DELETE FROM comments WHERE id = $id";
    $res = $conn->query($query);
}

//Preberi oglase iz baze
$ads = get_ads();

//Izpiši oglase
//Doda link z GET parametrom id na oglasi.php za gumb 'Preberi več'
foreach($ads as $ad){
    ?>
    <div class="ad panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo $ad->title;?></h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="data:image/jpg;base64, <?php echo base64_encode($ad->image);?>" class="img-responsive"/>
                </div>
                <div class="col-md-8">
                    <p><?php
                        global $conn;
                        $result = $conn->query("SELECT * FROM ad_categories JOIN categories ON categories.id = ad_categories.category_id WHERE ad_id = '$ad->id'");
                        // Fetch the results as an associative array
                        $categories = array();
                        while ($category = $result->fetch_object()) {?>
                            <?php echo $category->name; ?>
                        <?php } ?>
                    </p>
                    <p><?php echo $ad->description;?></p>
                    <a href="ad.php?id=<?php echo $ad->id;?>" class="btn btn-primary">Preberi več</a>
                    <form action="myAds.php" method="post" class="pull-right">
                        <input type="hidden" name="id" value="<?php echo $ad->id;?>">
                        <input type="submit" name="delete" value="Delete" class="btn btn-danger">
                    </form>
                    <form action="myAds.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $ad->id;?>">
                        <div class="form-group">
                            <label for="title">Naslov</label>
                            <input type="text" name="title" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="description">Vsebina</label>
                            <textarea name="description" class="form-control" rows="10" cols="50"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Slika</label>
                            <input type="file" name="image" class="form-control-file" />
                        </div>
                        <input type="submit" name="change" value="Change" class="btn btn-primary">
                    </form>

                    <p class="card-text">Komentarji:</p>
                    <p class="card-text">
                        <?php
                        global $conn;
                        $result = $conn->query("SELECT * FROM comments WHERE ad_id = '$ad->id'");
                        // Fetch the results as an associative array
                        $comments = array();
                        while ($comments = $result->fetch_object()) {?>
                            <?php echo $comments->text; ?><br>
                    <form action="myAds.php" method="post" class="pull-right">
                        <input type="hidden" name="commentId" value="<?php echo $comments->id;?>">
                        <input type="submit" name="deleteComment" value="Delete" class="btn btn-danger">
                    </form>
                        <?php } ?>
                    </p>


                </div>
            </div>
        </div>
    </div>
    <?php
}


include_once('footer.php');
?>
