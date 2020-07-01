<!DOCTYPE html>
<?php
    require_once('mysqli_connect.php');
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Juan Alfonso Chan Resume Registry</title>
</head>

<body>
<h1>Profile information</h1><hr>

<?php        
    $query = "SELECT first_name, last_name, email, headline, summary FROM misc.Profile where profile_id = ?";
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
    } else {
        echo '<br><b>First Name: </b>'. $fname;
        echo '<br><b>Last Name: </b>'. $lname;
        echo '<br><b>Email: </b>'. $em;
        echo '<br><b>Headline:<br> </b>'. $head;
        echo '<br><b>Summary:<br> </b>'. $sum;
        echo '<br><br><a href="index.php">Done</a>';
    }
    
    //Clean the stmt variable. Also, clean db when not in use
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);

?>
</body>