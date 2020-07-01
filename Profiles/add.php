<!DOCTYPE html>
<?php
require_once('mysqli_connect.php');
session_start();
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Juan Alfonso Chan Resume Registry</title>
</head>
<body>

    <?php
    if(isset($_POST['first_name']) && isset($_POST['last_name']) &&
       isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){

        $query = "INSERT INTO misc.Profile (user_id, first_name, last_name, email, headline, summary)
        VALUES(?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($dbc, $query);
        
        mysqli_stmt_bind_param($stmt, "isssss", $_SESSION['user_id'], $_POST['first_name'], $_POST['last_name'],
                               $_POST['email'], $_POST['headline'], $_POST['summary']);
        mysqli_stmt_execute($stmt);

        //Clean the stmt variable. Also, clean db when not in use
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);

        //RECORD ADDED, RETURN TO INDEX
        $_SESSION['success'] = 'Record Added';
        header('Location: index.php');
        return;
    }
    ?>

    <!-- HTML FORM -->
    <h1> Add a New User </h1>
    <hr>
    <form method="post"> 
        <b>Add User Profile </b>
        <p>First Name:
        <input type="text" name="first_name" size="20"></p>
        <p>Last Name:
        <input type="text" name="last_name" size="20"></p>
        <p>Email:
        <input type="text" name="email" size="20"></p>
        <p>Headline: <br>
        <input type="text" name="headline" size="30"></p>
        <p>Summary: <br>
        <input type="text" name="summary" value=""></p>

        <!-- Submit button -->
        <p><input type = "submit" name="submit" value="Add"/>
        <a href="index.php">Cancel</a></p>
        
    </form>
    
</body>

</html>