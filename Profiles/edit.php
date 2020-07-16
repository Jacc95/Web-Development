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

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class = "w3-panel w3-pale-red">
    <?php
    if(isset($_POST['submit'])){
        //Initialize missing field array
        $data_missing = array();
        
        //Start checking for missing fields
        if(empty($_POST['first_name']))
            $data_missing[] = 'First Name';
        if(empty($_POST['last_name']))
            $data_missing[] = 'Last Name';
        if(empty($_POST['email']))
            $data_missing[] = 'Email';
        if(empty($_POST['headline']))
            $data_missing[] = 'Headline';
        if(empty($_POST['summary']))
            $data_missing[] = 'Summary';
        
        if(!preg_match("/@/", $_POST["email"]))  
        
            $email_er = "<p>Email must contain @</p>";  
        

        if(empty($data_missing) && ($email_er == NULL)){    

            $query = "UPDATE misc.profile SET first_name = ?, last_name = ?,
                    email = ?, headline = ?, summary = ? WHERE profile_id = ?";
            $stmt = mysqli_prepare($dbc, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $_POST['first_name'], $_POST['last_name'],
                                $_POST['email'], $_POST['headline'], $_POST['summary'], 
                                $_GET['profile_id']);
            mysqli_stmt_execute($stmt);

            //Clean the stmt variable. Also, clean db when not in use
            mysqli_stmt_close($stmt);
            mysqli_close($dbc);

            //RECORD UPDATED, RETURN TO INDEX
            $_SESSION['success'] = 'Record updated';
            header('Location: index.php');
            return;
        } elseif (!empty($data_missing)){
            echo '<p><b> All fields are required </b><p>';
            echo '<ul>';
            foreach($data_missing as $missing){
                echo '<li> Missing '.$missing.'</li><br>';
            }
            echo '</ul>';
        } else{
            echo $email_er;
        }
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
</div>

    <h1>Edit User</h1><hr>
    <form method="post">
        <p>First Name:
        <input type="text" name="first_name" value="<?= $fname ?>" size="20"></p>
        <p>Last Name:
        <input type="text" name="last_name" value="<?= $lname ?>" size="20"></p>
        <p>Email:
        <input type="text" name="email" value="<?= $em ?>" size="25"></p>
        <p>Headline: <br>
        <input type="text" name="headline" value="<?= $head ?>" size="32"></p>
        <p>Summary: <br>
        <textarea name="summary" rows="5" cols="50" ><?= $sum ?></textarea></p>
        <p><input type = "submit" name="submit" value = "Save"/>
        <a href = "index.php">Cancel</a></p>
    </form>
    
    
</body>

</html>