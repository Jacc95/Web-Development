<!DOCTYPE html>
<?php
    require_once('mysqli_connect.php');
    session_start();
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Juan Alfonso Chan Chong</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">
</head>

<body style="font-family: sans-serif;">
<h1>Please Log In </h1><hr>
<?php
    if (isset($_SESSION['error'])){
        echo'<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])){
        echo'<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }
    
    //email validation using mySQL DB
    if (isset($_POST['submit'])){
        unset($_SESSION['email']); //Logout current user

        $salt = 'XyZzy12*_';
        $check = hash('md5', $salt.$_POST['pass']);

        $query = "SELECT user_id, name FROM misc.users WHERE email = ? AND password = ?";
        $stmt = mysqli_prepare($dbc, $query);        
        mysqli_stmt_bind_param($stmt, "ss", $_POST['email'], $check);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result($stmt, $uid, $name);
        $row = mysqli_stmt_fetch($stmt);
        if ($row != false){
            //Clean the stmt variable. Also, clean db when not in use
            mysqli_stmt_close($stmt);
            mysqli_close($dbc);

            //SESSION VARIABLES SETUP & RETURN TO INDEX
            $_SESSION['email'] = $name;
            $_SESSION['user_id'] = $uid;
            $_SESSION['success'] = "Logged in.";
            header('Location: index.php');
            return;
        } else{
            //KEEP TRYING IF THE INPUTS ARE NOT CORRECT
            $_SESSION["error"] = "Incorrect email or password.";
            header( 'Location: login.php' ) ;
            return;
        }
    }
?>

<div class="container">
<!-- HTML FORM -->
<form method="post">
<p>Email: <input type="text" name="email" value=""></p>
<p>Password: <input type="password" name="pass" value="" id="id_1723"></p>
<p><input type="submit" onclick="return doValidate();" name="submit" value="Log In">
<!-- <button onclick = "document.location='index.php';">Cancel</button> -->
<a href="index.php">Cancel</a></p>
</form>
</div>

<script>
function doValidate(){
    console.log('Validating...');
    try{
        pass = document.getElementById('id_1723').value;
        console.log("Validating pw="+pass);
        if(pass == null || pass == ""){
            alert("Both fields must be filled out");
            return false;
        }
        return true;
    
    } catch(err){
        return false;
    }
}
</script>
</body>
</html>