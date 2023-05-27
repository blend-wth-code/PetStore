<?php
    function checkSession($dbc, $email){
        $query = "SELECT COUNT(1) FROM USERS WHERE EMAIL = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$rows);
        mysqli_stmt_fetch($stmt);
        return $rows;
    }

    function login($dbc, $email, $password){
        $query = "SELECT COUNT(1) FROM USERS WHERE EMAIL = ? AND PASSWORD = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$rows);
        mysqli_stmt_fetch($stmt);
        return $rows;
    }