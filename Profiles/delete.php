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
    if(isset($_POST['delete']) && isset($_GET['profile_id']) ){
        
        $query = "DELETE FROM misc.profile WHERE profile_id = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, "i", $_GET['profile_id']);
        mysqli_stmt_execute($stmt);

        //Clean the stmt variable. Also, clean db when not in use
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);

        $_SESSION['success'] = 'Record deleted';
        header('Location: index.php');
        return;
    }

    $query = "SELECT first_name, last_name FROM misc.profile where profile_id = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['profile_id']);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $fname, $lname);
    $row = mysqli_stmt_fetch($stmt);
    if($row != true){
        $_SESSION['error'] = 'Bad value for profile_id';
        header( 'Location: index.php');
        return;
    } 
    ?>

    <h1>Delete Profile</h1><hr>
    <p><b>Confirm:</b> Deleting <?php echo $fname.' '.$lname ?></p>

    <form method="post"><input type="hidden"
    name="profile_id" value="<?php $_GET['profile_id'] ?>">
    <input type="submit" value="Delete" name="delete">
    <a href="index.php">Cancel</a>
    </form>
    
    
</body>

</html>