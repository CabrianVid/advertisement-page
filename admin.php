<?php
include_once('header.php');

// Funkcija prebere uporabnike iz baze in vrne polje objektov
function get_users(){
    global $conn;// v headerju smo mogli spostavit povezavo s bazo
    $query = "SELECT * FROM users ORDER BY id DESC;";//zberamo vse oglase
    $res = $conn->query($query);
    $users = array();
    while($user = $res->fetch_object()){
        array_push($users, $user);
    }
    return $users;
}

function change_user($user_id, $username, $password, $first_name, $last_name, $email, $address, $post, $phone_number)
{
global $conn;
$query = "UPDATE users SET username = '$username', password = '$password', firstName = '$first_name', lastName = '$last_name', email = '$email', address = '$address', post = '$post', phoneNumber = '$phone_number' WHERE id ='$user_id'";
$res = $conn->query($query);
if($res){
    return true;
} else {
    return false;
}
}


if(isset($_POST["userChange"])){
    //change ad
    if(change_user($_POST["user_id"], $_POST["username"], $_POST["password"], $_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["address"], $_POST["post"], $_POST["phone_number"])){
        header("Location: index.php");
        die();
    }	else{
        $error = "Prišlo je do napake pri spremembi  oglasa.";
    }

}
if(isset($_POST["delete"])){
    delete_ad($_POST["user_id_delete"]);

}

function delete_ad($id){
    global $conn;
    $query = "DELETE FROM users WHERE id = $id";
    $res = $conn->query($query);
}


// Preberi uporabnike iz baze
$users = get_users();

// Izpiši uporabnike
foreach($users as $user){
?>
<div class="ad panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><?php echo $user->username;?></h4>
    </div>
    <div class="panel-body">
        <form method="post" action="admin.php" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo $user->id; ?>">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" class="form-control" name="username" value="<?php echo $user->username;?>">
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" class="form-control" name="password" ">
            </div>
            <div class="form-group">
                <label>First name:</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo $user->firstName;?>">
            </div>
            <div class="form-group">
                <label>Last name:</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo $user->lastName;?>">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo $user->email;?>">
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" class="form-control" name="address" value="<?php echo $user->address;?>">
            </div>
            <div class="form-group">
                <label>Post:</label>
                <input type="text" class="form-control" name="post" value="<?php echo $user->post;?>">
            </div>
            <div class="form-group">
                <label>Phone number:</label>
                <input type="text" class="form-control" name="phone_number" value="<?php echo $user->phoneNumber;?>">
            </div>
            <button type="submit" name="userChange" class="btn btn-primary">Save changes</button>
        </form>
        <form action="admin.php" method="post" class="pull-right">
            <input type="hidden" name="user_id_delete" value="<?php echo $user->id;?>">
            <input type="submit" name="delete" value="Delete" class="btn btn-danger">
        </form>
    </div>
</div>
<hr/>
<?php
}
