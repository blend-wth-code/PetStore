<?php
    require('../db/dbinit.php');
    session_start();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['prodId']) && isset($_POST['quantity'])){
            $id = prepare_string($dbc, strval($_POST['prodId']));
            $quantity = prepare_string($dbc, strval($_POST['quantity']));
            $quantity = intval($quantity);
            // $id = intval($quantity);  
            $id = intval($id);  

            // Check if the product already exists in the orders table
            $query = "SELECT COUNT(*) FROM orders WHERE prod_id = ? AND email = ?";
            $stmt = mysqli_prepare($dbc, $query);
            mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['email']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $count);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_free_result($stmt);

            if ($count == 0) {
                // If the product doesn't exist, insert a new row into the orders table
                $query = "INSERT INTO orders (prod_id, email, quantity) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($dbc, $query);
                mysqli_stmt_bind_param($stmt, 'isi', $id, $_SESSION['email'], $quantity);
                mysqli_stmt_execute($stmt);
            } else {
                // If the product already exists, update the quantity column of the existing row
                if($quantity == 0){
                    $query = "DELETE FROM orders WHERE prod_id = ? AND email = ?";
                    $stmt = mysqli_prepare($dbc, $query);
                    mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['email']);
                }
                else{
                    $query = "UPDATE orders SET quantity = ? WHERE prod_id = ? AND email = ?";
                    $stmt = mysqli_prepare($dbc, $query);
                    mysqli_stmt_bind_param($stmt, 'iis', $quantity, $id, $_SESSION['email']);
                }
                mysqli_stmt_execute($stmt);
            }

            if (mysqli_stmt_affected_rows($stmt) == 0) {
                echo json_encode(false);
            } else {
                // echo json_encode(true);
                $response = array("success" => true, "quantity" => $quantity);
                echo json_encode($response);
            }
        }
    }