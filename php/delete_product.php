<?php
    require('../db/dbinit.php');
    session_start();
    if(!isset($_SESSION['isAdmin'])){
        header("Location: login.php");
        return;
    }
    $error = null;

    if(!empty($_GET['id'])){
        $id = prepare_string($dbc, $_GET['id']);
    } else {
        $id = null;
        $error = "<p> Product Id not found!</p>";
    }

    if($error == null){
        $stmt = mysqli_prepare($dbc, "DELETE FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_affected_rows($stmt);
        if($result){
            header("Location: admin.php");
            exit;
        } else {
            echo "</br><p>Some error in Deleting the record</p>";
        }
        
    } else{
        echo "Something went wrong. The error is : $error";
    }
?>