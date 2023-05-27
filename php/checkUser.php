<?php
    include_once("../db/dbinit.php");
    if($_SERVER["REQUEST_METHOD"] == 'POST'){
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if(isset($_POST['email'])){
            $email = prepare_string($dbc, $_POST['email']);
            if(!$email){
                echo json_encode("Email is required");
                return;
            }
            else if(preg_match($pattern, $email) === 0){
                echo json_encode("Invalid Email");
                return;
            }
            $ans = checkEmailExists($dbc, $email);
            echo $ans;
        }
    }

    function checkEmailExists($dbc, $email){
        $query = "SELECT COUNT(1) FROM USERS WHERE EMAIL = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $rows);
        mysqli_stmt_fetch($stmt);
        header("Content-Type: application/json");
        if ($rows > 0) {
          echo json_encode("Email already exists");
        } else {
          echo json_encode("Valid");
        }
    }