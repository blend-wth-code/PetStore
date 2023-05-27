<!DOCTYPE html>
<html lang="en">
    <?php 
        session_start();
        if(!isset($_SESSION['isAdmin'])){
            header("Location: login.php");
            session_destroy();
            return;
        }
    ?>
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/checkoutStyle.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <title>
            Checkout
        </title>
    </head>
    <body>
        <?php 
            include_once('./header.php');
            include_once('../db/dbinit.php');
            if($_SESSION['isAdmin'] > 0){
                header("Location: products.php");
                return;
            }

            $query = "SELECT P.PRICE PRICE, O.QUANTITY QUANTITY FROM ORDERS O JOIN PRODUCTS P ON O.PROD_ID = P.ID WHERE EMAIL = ? AND STATUS = 0";
            $stmt = mysqli_prepare($dbc, $query);
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
            // Execute the statement and get the result set
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $total = 0;
            while($product = mysqli_fetch_assoc($result)){
                $total += ($product['PRICE'] * $product['QUANTITY']);
            }
            $isValid = true;
            $error = ["firstName" => "", "lastName" => "", "address" => "", "city" => '', "zipcode" => "",  "country" => "", "cardHolderName" => '', "cardNumber" => "", "expiry" => '', "cvv" => "", "err" => ""];
            $fname = $lname = $address = $city = $country = $zipcode = $cardHolderName = $cardNumber = $cvv = $expiry = "";

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
                if (isset($_POST["address"])) {
                    $address = prepare_string($dbc, trim($_POST["address"]));
                    if(!$address){
                        $error["address"] = "Address is required";
                        $isValid = false;
                    }
                    else if(strlen($address) < 3){
                        $error["address"] = "Address should be atleast 3 chars";
                        $isValid = false;
                    }
                }
                else{
                    $error["address"] = "Address is required";
                    $isValid = false;
                }

                if(isset($_POST["city"])){
                    $city = prepare_string($dbc, trim($_POST['city']));
                    if(!$city){
                        $error["city"] = "City is required";
                        $isValid = false;
                    }
                }
                else{
                    $error["city"] = "City is required";
                    $isValid = false;
                }

                if(isset($_POST["country"])){
                    $country = prepare_string($dbc, trim($_POST['country']));
                    if(!$country){
                        $error["country"] = "Country is required";
                        $isValid = false;
                    }
                }
                else{
                    $error["country"] = "Country is required";
                    $isValid = false;
                }


                if(isset($_POST["zipcode"])){
                    $zipcode = prepare_string($dbc, trim($_POST['zipcode']));
                    if(!$zipcode){
                        $error["zipcode"] = "Zipcode is required";
                        $isValid = false;
                    }
                    elseif(!ctype_digit($zipcode)){
                        $error["zipcode"] = "Zipcode must contain digits";
                        $isValid = false;
                    }
                    elseif(strlen($zipcode) != 5){
                        $error["zipcode"] = "Zipcode should be exactly 5 digits long";
                        $isValid = false;
                    }
                }
                else{
                    $error["zipcode"] = "Zipcode is required";
                    $isValid = false;
                }
                if(isset($_POST["cardHolderName"])){
                    $cardHolderName = prepare_string($dbc, trim($_POST['cardHolderName']));
                    if(!$cardHolderName){
                        $error["cardHolderName"] = "Card Holder Name is required";
                        $isValid = false;
                    }
                    else if(strlen($cardHolderName) < 3){
                        $error["cardHolderName"] = "Card Holder Name should be atleast 3 chars";
                        $isValid = false;
                    }
                }
                else{
                    $error["cardHolderName"] = "Card Holder Name is required";
                    $isValid = false;
                }

                if(isset($_POST["cardNumber"])){
                    $cardNumber = prepare_string($dbc, trim($_POST['cardNumber']));
                    if(!$cardNumber){
                        $error["cardNumber"] = "Card Number is required";
                        $isValid = false;
                    }
                    elseif(!ctype_digit($cardNumber)){
                        $error["cardNumber"] = "Card Number must contain digits";
                        $isValid = false;
                    }
                    elseif(strlen($cardNumber) != 16){
                        $error["cardNumber"] = "Card Number should be exactly 16 digits long";
                        $isValid = false;
                    }
                }
                else{
                    $error["cardNumber"] = "Card Number is required";
                    $isValid = false;
                }

                if(isset($_POST["cvv"])){
                    $cvv = prepare_string($dbc, trim($_POST['cvv']));
                    if(!$cvv){
                        $error["cvv"] = "CVV is required";
                        $isValid = false;
                    }
                    elseif(!ctype_digit($cvv)){
                        $error["cvv"] = "CVV must contain digits";
                        $isValid = false;
                    }
                    elseif(strlen($cvv) != 3){
                        $error["cvv"] = "CVV should be exactly 3 digits long";
                        $isValid = false;
                    }
                }
                else{
                    $error["cvv"] = "CVV is required";
                    $isValid = false;
                }

                if(isset($_POST["expiry"])){
                    $expiry = prepare_string($dbc, trim($_POST['expiry']));
                }
                else{
                    $error["expiry"] = "Expiry is required";
                    $isValid = false;
                }


                if($isValid){
                    $query = "UPDATE ORDERS SET STATUS = 1 WHERE EMAIL = ?";
                    $stmt = mysqli_prepare($dbc, $query);
                    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
                    mysqli_stmt_execute($stmt);

                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        header("Location: generate-invoice.php");
                        exit();
                    }
                }
            }
            
            ?>
    <main class="d-flex justify-content-center align-items-center">
        <?php 
            if($total == 0) {
                echo "<h2 class='empty'>You have no items to checkout</h2>";
            }
            else{
        ?>
            <h1 class="d-inline-block m-0">Checkout</h1>
            <div class="checkout">
                <div class="list-item">
                    <div class="details">
                        <div class="product-details">
                            <div class="d-flex flex-row justify-content-end">
                                <span class="product-price">Cart Value:</span>
                                <span class="product-price"><?php echo "$" . number_format($total,2) ?></span>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="product-price">Delivery:</span>
                                <span class="product-price"><?php echo "$10" ?></span>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="product-price">Tax (15%):</span>
                                <span class="product-price"><?php echo "$" . number_format($total * 0.15, 2) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="product-price font-weight-bold">Total: </span>
                                <span class="product-price font-weight-bold"><?php echo "$" . number_format($total * 0.15, 2) + ($total) + 10 ?></span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <form method="POST" class="d-inline checkout-form">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo $fname ?>">
                            <span class='text-danger w-100'><?php echo $error["firstName"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo $lname ?>">
                            <span class='text-danger'><?php echo $error["lastName"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="address" name="address" value="<?php echo $address ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["address"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="city" name="city" value="<?php echo $city ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["city"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="country" name="country" value="<?php echo $country ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["country"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="zipcode">Zipcode:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="number" id="zipcode" name="zipcode" value="<?php echo $zipcode ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["zipcode"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardHolderName">Card Holder Name:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="cardHolderName" name="cardHolderName" value="<?php echo $cardHolderName ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["cardHolderName"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="number" id="cardNumber" name="cardNumber" value="<?php echo $cardNumber ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["cardNumber"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="password" id="cvv" name="cvv" value="<?php echo $cvv ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["cvv"] ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="expiry">Expiry Date:</label>
                        <div class="d-flex flex-column justify-content-start">
                            <input type="text" id="expiry" name="expiry" value="<?php echo $expiry ?>" class="form-control">
                            <span class='text-danger'><?php echo $error["expiry"] ?></span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <input type="submit" class="bg-primary text-white border-0 p-2 rounded-lg m-auto" name="submit" value="Place Order">
                    </div>
                </form>
            </div>
        <?php 
            }
        ?>
    </main>
        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>
        <script>
            $(function() {
                $('#expiry').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    dateFormat: 'mm/y',
                    minDate: 0 
                });
            });
        </script>
    </body>
</html>