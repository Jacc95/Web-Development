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
    
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

</head>
<body>

<div class = "w3-panel w3-pale-red">
    <?php
    if(isset($_POST['submit'])){
        //Initialize missing field array
        $data_missing = array();
        $email_er = NULL;

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
            
            //Catches the last profile_id created to be used as the foreign key for the Position table
            $profile_id = mysqli_insert_id($dbc);

            
            $ranking = 1;
            for($i=1; $i<=9; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;

            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $query2 = "INSERT INTO misc.Position (profile_id, ranking, year, description)
            VALUES (?, ?, ?, ?)";
            $stmt2 = mysqli_prepare($dbc, $query2);
            mysqli_stmt_bind_param($stmt2, "iiis", $profile_id, $ranking, $year, $desc);
            mysqli_stmt_execute($stmt2);

            $ranking++;
            }

            //Clean the stmt variable. Also, clean db when not in use
            mysqli_stmt_close($stmt);
            mysqli_stmt_close($stmt2);
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
        <p>Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
        </div>
        </p>

        <!-- Submit button -->
        <p><input type = "submit" name="submit" value="Add"/>
        <a href="index.php">Cancel</a></p>
        
    </form>
</div>

<!-- JS Add position call -->
<script>
    countPos = 0;
    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event){
            event.preventDefault();
            if(countPos >= 9){
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position "+countPos);
            $('#position_fields').append('<div id="position'+countPos+'"> \
            <p>Year: <input type="number" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p>\
            <textarea name="desc'+countPos+'" rows="8" cols="60"></textarea> </div>');
        });
    });
</script>
</body>
</html>