<?php
include_once('header.php');

// Funkcija prebere oglase iz baze in vrne polje objektov
function get_ads(){
    global $conn;// v headerju smo mogli spostavit povezavo s bazo
    $query = "SELECT * FROM ads ORDER BY published_date DESC;";//zberamo vse oglase
    $res = $conn->query($query);
    $ads = array();
    while($ad = $res->fetch_object()){
        array_push($ads, $ad);
    }
    return $ads;
}
//___________________________________--
function addComment($user_id, $ad_id, $comment){
    global $conn;
    $query = "INSERT INTO comments (text, user_id, ad_id)VALUES('$comment','$user_id', '$ad_id');";//shranimo podatke v bazo

    if($conn->query($query)){
        return true;
    }
    else{
        echo mysqli_error($conn);
        return false;
    }

}



if(isset($_POST["submitComment"])){
//       $user_id = $_SESSION["USER_ID"];
    if(addComment($_POST["user_id1"], $_POST["ad_id1"], $_POST["comment"])){
        header("Location: index.php");
        die();
    }	else{
        $error = "Prišlo je do napake pri objavi komentarja.";
    }

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
            <p class="text-muted">
                <?php
                global $conn;
                $result = $conn->query("SELECT * FROM ad_categories JOIN categories ON categories.id = ad_categories.category_id WHERE ad_id = '$ad->id'");
                // Fetch the results as an associative array
                $categories = array();
                while ($category = $result->fetch_object()) {?>
                    <?php echo $category->name; ?>
                <?php } ?>
            </p>
            <img src="data:image/jpg;base64, <?php echo base64_encode($ad->image);?>" class="img-responsive" alt="Ad Image" />
            <p>
                <a href="ad.php?id=<?php echo $ad->id;?>" class="btn btn-primary">Preberi več</a>
            </p>
            <p> _________________________________ </p>
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
                        <?php
            if(isset($_SESSION["USER_ID"])){
                        ?>

            <form action="index.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="hidden" name="user_id1" value="<?php echo $_SESSION["USER_ID"];?>">
                    <input type="hidden" name="ad_id1" value="<?php echo $ad->id;?>">
<!---->
<!--                    <p class="card-text">user id: --><?php //echo $_SESSION["USER_ID"];?><!--</p>-->
<!--                    <p class="card-text">ad id: --><?php //echo $ad->id;?><!--</p>-->

                    <label for="title">Komentar</label>
                    <input type="text" name="comment" class="form-control"/>
                </div>
                <input type="submit" name="submitComment" value="Objavi Komentar" class="btn btn-primary mb-3" />

            </form>
                <?php }?>
            <a href="index.php" class="btn btn-primary">Nazaj</a>

            </p>
        </div>
    </div>
    <hr/>
    <?php
}


include_once('footer.php');
?>
