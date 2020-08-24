<!DOCTYPE html>
<?php
    require_once('mysqli_connect.php');
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
    
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>

<body>
<div class="container">
<h1>Profile information</h1><hr>

<?php        
    $query = "SELECT first_name, last_name, email, headline, summary FROM misc.Profile where profile_id = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['profile_id']);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $fname, $lname, $em, $head, $sum);   //Bind variables to the table columns for future assignment
    $row = mysqli_stmt_fetch($stmt);    //Assigns the table columns data to the variable
    mysqli_stmt_close($stmt);

    $query = "SELECT year, description FROM misc.Position WHERE (profile_id = ?) AND (ranking = ?)";
    $stmt = mysqli_prepare($dbc, $query);

    for($i=1; $i<=9; $i++) {
        $yr = NULL;
        mysqli_stmt_bind_param($stmt, "ii", $_GET['profile_id'], $i);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $yr, $dsc);   //Bind variables to the table columns for future assignment
        mysqli_stmt_fetch($stmt);       //Assigns the table columns data to the variable
        $year_data[$i-1] = $yr;
        $desc_data[$i-1] = $dsc;
    }

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
        echo '<br><b>Position:<br> </b><ul>';
        $count = 0;
        foreach($year_data as $Year){
            if($Year != NULL){
            echo '<li> '.$Year;
            echo ': '.$desc_data[$count];
            echo '</li>';
            $count++;
            }
        }
        echo '</ul><a href="index.php">Done</a>';
    }
    
    //Clean the stmt variable. Also, clean db when not in use
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);

?>
</div>
</body>