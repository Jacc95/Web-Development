<!DOCTYPE html>
<?php
    require_once('mysqli_connect.php');
    session_start();
?>
<html>
<head>
<title>Juan Alfonso Chan Chong</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Juan Alfonso Chan - Resume Registry</h1><hr>

<!-- LOGIN/LOGOUT FEATURE -->
<?php 
        if (isset($_SESSION['error'])){
            echo'<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])){
            echo'<p style="color:green">'.$_SESSION['success']."</p>\n";
            unset($_SESSION['success']);
        }

        if(!isset($_SESSION["email"])){ 
        echo '<p><a href="login.php">Please log in</a></p>';
        }
        else{
        echo '<p><a href="logout.php">Logout</a></p>';
        }
?>

<!-- MAIN LOGIC AND TABLE SHOWCASE -->
<?php        
    $query = "SELECT first_name, last_name, headline, profile_id
    FROM misc.Profile";

    $response = @mysqli_query($dbc, $query);

    if($response){
        echo '<table border ="1" align="left" cellspacing="5" cellpadding="8">
        
        <tr><td align="left"><b>Name</b></td>
        <td align="left"><b>Headline</b></td>';
        if(isset($_SESSION["email"])){
            echo '<td align="left"><b>Action</b></td>';
        }
        echo '</tr>';
        
        while($row = mysqli_fetch_array($response)){
            echo '<tr><td align = "left">' ; 
            echo '<a href="view.php?profile_id='.$row['profile_id'].'">'.
            $row['first_name'] . ' ' .
            $row['last_name'] . '</a>' .'</td><td align="left">' .
            $row['headline'] . '</td><td align="left">' ;
            if(isset($_SESSION["email"])){
                echo '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>' . ' / ' ;  
                echo '<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>' .
                '</td><td align="left">';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    mysqli_close($dbc);
?>

</div>
<br>
<!-- SHOW 'ADD NEW' OPTION WHEN LOGGED IN -->
<?php if(isset($_SESSION["email"])){ 
    echo '<div class = "container"><a href="add.php">Add New Entry</a></div>';
    }
?>
</body>