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
    isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ){
        

        $query = "UPDATE misc.profile SET first_name = ?, last_name = ?
                  email = ?, headline = ?, summary = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $_POST['first_name'], $_POST['last_name'],
                               $_POST['email'], $_POST['headline'], $_POST['summary']);
        mysqli_stmt_execute($stmt);

        //Clean the stmt variable. Also, clean db when not in use
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);

        //RECORD UPDATED, RETURN TO INDEX
        $_SESSION['success'] = 'Record updated';
        header('Location: index.php');
        return;
    }

    $query = "SELECT first_name, last_name, email, headline, summary FROM misc.profile where profile_id = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['profile_id']);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $fname, $lname, $em, $head, $sum);
    $row = mysqli_stmt_fetch($stmt);
    if($row != true){
        $_SESSION['error'] = 'Bad value for profile_id';
        mysqli_stmt_close($stmt);
        header( 'Location: index.php');
        return;
    } 
    ?>

    <h1>Edit User</h1><hr>
    <form method="post">
    <b>Edit User Profile </b>
        <p>First Name:
        <input type="text" name="first_name" value="<?= $fname ?>" size="20"></p>
        <p>Last Name:
        <input type="text" name="last_name" value="<?= $lname ?>" size="20"></p>
        <p>Email:
        <input type="text" name="email" value="<?= $em ?>" size="20"></p>
        <p>Headline: <br>
        <input type="text" name="headline" value="<?= $head ?>" size="30"></p>
        <p>Summary: <br>
        <input type="text" name="summary" value="<?= $sum ?>"></p>
        <p><input type = "submit" value = "Update"/>
        <a href = "index.php">Cancel</a></p>
    </form>
    
    
</body>

</html>