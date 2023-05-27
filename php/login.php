<!DOCTYPE html>
<html lang="en">
   <?php
        session_start();
    ?>
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/indexStyle.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <title>
            Login
        </title>
        <?php
            include_once("../db/dbinit.php");
            include_once("./utils.php");
            
            if(isset($_SESSION['email'])){
                $rows = checkSession($dbc, $_SESSION['email']);
                if($rows == 1){
                    header("Location: products.php");
                }
            }
            else{
                session_destroy();
            }
        ?>
    </head>
    <body>
        <div class="header">
            <header class="headerTitle">
                Snoopy's Pet Supplies
            </header>
        </div>
        <main>
            <?php
                $error = ["email" => "", "password" => '', "err" => ""];
                $isValid = true;
                $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
                $email = "";
                $password = "";
                if($_SERVER["REQUEST_METHOD"] == 'POST'){
                    
                    if (isset($_POST["email"])) {
                        $email = prepare_string($dbc, trim($_POST["email"]));
                        if(!$email){
                            $error["email"] = "Email is required";
                            $isValid = false;
                        }
                        else if (preg_match($pattern, $email) === 0) {
                            $error["email"] = "Invalid email address";
                            $isValid = false;
                        }
                    }
                    else{
                        $error["email"] = "Email is required";
                        $isValid = false;
                    }

                    if(isset($_POST["password"])){
                        $password = prepare_string($dbc, trim($_POST['password']));
                        if(!$password){
                            $error["password"] = "Password is required";
                            $isValid = false;
                        }
                        else if(strlen($password) < 5){
                            $error["password"] = "Password should be atleast 5 chars";
                            $password = "";
                            $isValid = false;
                        }
                    }
                    else{
                        $error["password"] = "Password is required";
                        $isValid = false;
                    }

                    if($isValid){
                        $selectQuery = "SELECT COUNT(1) FROM USERS WHERE EMAIL = ?";
                        $selectStmt = mysqli_prepare($dbc, $selectQuery);
                        mysqli_stmt_bind_param($selectStmt, "s", $email);
                        mysqli_stmt_execute($selectStmt);
                        mysqli_stmt_bind_result($selectStmt, $rowCount);
                        mysqli_stmt_fetch($selectStmt);
                        mysqli_stmt_free_result($selectStmt);
                        if(!$rowCount){
                            $error["email"] = "Email is not registered";
                        }
                        else{
                            $rows = login($dbc, $email, $password);
                            echo $rows;
                            if($rows == 1){
                                session_start();
                                $_SESSION['email'] = $email;
                                if($email == "admin@petshop.com"){
                                    $_SESSION['isAdmin'] = 1;
                                }
                                else {
                                    $_SESSION['isAdmin'] = -1;
                                }
                                header("Location: products.php");
                            }
                            else{
                                $error["err"] = "Username and password did not match";
                            }
                        }
                    }
                }
            ?>
            <div class="container-fluid">
            <div class="justify-content-center">
                <h2 class="my-4 text-center text-uppercase">Login</h2>
                <form method="POST" class="border-2 w-50 m-auto">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>" placeholder="Enter Email">
                        <span class='text-danger'><?php echo $error["email"] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" value="<?php echo $password ?>" placeholder="Enter Password">
                        <span class='text-danger'><?php echo $error["password"] ?></span>
                        <span class='text-danger'><?php echo $error["err"] ?></span>
                    </div>
                    <div class="form-group d-flex justify-content-center align-items-center mb-0">
                        <button type="submit" class="btn btn-primary mx-2">Login</button>
                        <a href="./register.php">Register here</a>
                    </div>

                </form>
            </div>
            </div>
        </main>
        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>
    </body>
</html>