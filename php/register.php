<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/indexStyle.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <title>
            Register
        </title>
        <?php
            include_once("../db/dbinit.php");
            include_once("./utils.php");
            session_start();

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
                $error = ["firstName" => "", "lastName" => "", "email" => "", "password" => '', "confirmPassword" => "", "err" => ""];
                $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
                $isValid = true;
                $fname = "";
                $lname = "";
                $email = "";
                $password = "";
                $confirmPassword = "";
                if($_SERVER["REQUEST_METHOD"] == 'POST'){
                    if(isset($_POST["firstName"])){
                        $fname = prepare_string($dbc, trim($_POST['firstName']));
                        if(!$fname){
                            $error["firstName"] = "First Name is required";
                            $isValid = false;
                        }
                        else if(strlen($fname) < 3){
                            $error["firstName"] = "First Name should be atleast 3 chars";
                            $isValid = false;
                        }
                    }
                    else{
                        $error["firstName"] = "First Name is required";
                        $isValid = false;
                    }

                    if(isset($_POST["lastName"])){
                        $lname = prepare_string($dbc, trim($_POST['lastName']));
                        if(!$lname){
                            $error["lastName"] = "Last Name is required";
                            $isValid = false;
                        }
                        else if(strlen($lname) < 3){
                            $error["lastName"] = "Last Name should be atleast 3 chars";
                            $isValid = false;
                        }
                    }
                    else{
                        $error["lastName"] = "Last Name is required";
                        $isValid = false;
                    }
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

                    if(isset($_POST["confirmPassword"])){
                        $confirmPassword = prepare_string($dbc, trim($_POST['confirmPassword']));
                        if(!$confirmPassword){
                            $error["confirmPassword"] = "Confirm Password is required";
                            $isValid = false;
                        }
                        else if(strlen($confirmPassword) < 5){
                            $error["confirmPassword"] = "Confirm Password should be atleast 5 chars";
                            $confirmPassword = "";
                            $isValid = false;
                        }
                        else if($password !=  $confirmPassword){
                            $error["confirmPassword"] = "Password did not match Confirm Password";
                            $isValid = false;
                        }
                    }
                    else{
                        $error["confirmPassword"] = "Confirm Password is required";
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
                        if($rowCount){
                            $error["email"] = "Email Already Exists";
                        }
                        else{
                            try{
                                $query = "INSERT INTO USERS (`FIRST_NAME`, `LAST_NAME`, `EMAIL`, `PASSWORD`) VALUES (?, ?, ?, ?)";
                                $stmt = mysqli_prepare($dbc, $query);
                                mysqli_stmt_bind_param($stmt, "ssss", $fname, $lname, $email, $password);
                                mysqli_stmt_execute($stmt);
                                $rows=mysqli_stmt_affected_rows($stmt);
                                if($rows == 1){
                                    header("Location: login.php");
                                    mysqli_close($dbc);
                                    exit;
                                }
                                else{
                                    $error["err"] = "Error occurred while registration";
                                }
                            }
                            catch(Exception $e){
                                $error["err"] = "Error occurred while registration " . $e->getMessage();
                            }
                        }
                    }
                }
               
            ?>
            <div class="container-fluid">
            <div class="justify-content-center mb-2">
                <h2 class="my-2 text-center text-uppercase">Register</h2>
                <form method="POST" action="register.php" class="border-2 w-50 m-auto align-content-center">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $fname ?>" placeholder="Enter First Name">
                        <span class='text-danger'><?php echo $error["firstName"] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lname ?>" placeholder="Enter Last Name">
                        <span class='text-danger'><?php echo $error["lastName"] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" onfocusout="checkEmailAPI()" class="form-control" id="email" name="email" value="<?php echo $email ?>" placeholder="Enter Email">
                        <span id="emailErr" class='text-danger'><?php echo $error["email"] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" value="<?php echo $password ?>" placeholder="Enter Password">
                        <span class='text-danger'><?php echo $error["password"] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"  value="<?php echo $confirmPassword ?>" placeholder="Confirm Password">
                        <span class='text-danger'><?php echo $error["confirmPassword"] ?></span>
                    </div>
                    <div class="form-group d-flex justify-content-center align-items-center mb-0">
                        <button type="submit" class="btn btn-primary mx-2">Register</button>
                        <a href="./login.php">Login here</a>
                    </div>
                    <span class='text-danger'><?php echo $error["err"] ?></span>
                </form>
            </div>
            </div>
        </main>
        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>

        <script>
            function checkEmailAPI(){
                document.getElementById("emailErr").innerHTML = "";

                let email = document.getElementById("email").value;
                if(email){
                    email = email.trim();
                }
                else{
                    document.getElementById("emailErr").innerHTML = "Invalid Email";
                    return;
                }
                const form = new FormData();
                form.append("email", email);
                fetch("/p/php/checkUser.php", {
                    method: "POST",
                    header: {
                        "Content-Type": "application/json"
                    },
                    body: form
                })
                .then(resp => resp.json())
                .then(resp => {
                    if(resp?.toLowerCase() != "valid"){
                        document.getElementById("emailErr").innerHTML = resp;
                    }
                })
            }
        </script>
    </body>
</html>