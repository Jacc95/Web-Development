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
        } elseif(!empty($data_missing)){
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
    ?>
    </div>

<div class="container">
    <!-- HTML FORM -->
    <h1> Add a New User </h1>
    <hr>
    <form method="post"> 
        <p>First Name:
        <input type="text" name="first_name" size="20"></p>
        <p>Last Name:
        <input type="text" name="last_name" size="20"></p>
        <p>Email:
        <input type="text" name="email" size="25"></p>
        <p>Headline: <br>
        <input type="text" name="headline" size="32"></p>
        <p>Summary: <br>
        <textarea name="summary" rows="5" cols="50"></textarea></p>

        <!-- Submit button -->
        <p><input type = "submit" name="submit" value="Add"/>
        <a href="index.php">Cancel</a></p>
        
    </form>
</div>
</body>

</html>