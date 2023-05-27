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
        <link rel="stylesheet" href="../css/cartStyle.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <title>
            Cart
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
            
            $query = 'SELECT P.ID, P.IMAGENAME, P.PRICE, O.QUANTITY, P.NAME FROM PRODUCTS P JOIN ORDERS O ON P.ID = O.PROD_ID WHERE EMAIL = ? AND P.QUANTITY > 0 AND O.STATUS = 0'; 
            $stmt = mysqli_prepare($dbc, $query);
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
            // Execute the statement and get the result set
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        ?>
        </div>
        <main>
            <div class="mt-5 mx-5 ">
                <?php 
                    if(mysqli_num_rows($result) == 0) {
                        echo "<h2 class='empty'>Your cart is empty</h2>";
                    }
                    else{
                    ?>
                    <h2>Your Cart</h2>
                    <?php 
                        while ($product = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="list-item d-flex justify-content-between w-50">
                            <div class="details">
                                <img class="circular-image" src="<?php echo "../images/".$product['IMAGENAME'] ?>" alt="Product Image">
                                <div class="product-details d-inline-block ms-2">
                                    <h3 class="product-name"><?php echo $product['NAME'] ?></h3>
                                    <span class="product-price"><?php echo "$" . $product['PRICE'] ?></span>
                                </div>
                            </div>
                            <div class="quantity">
                                <button class="qty-btn minus w-25" onclick="<?php echo "decreaseQuantity(".$product['ID']. ")"; ?>">-</button>
                                <span class="qty-input" id="<?php echo $product['ID'].'_quantity'; ?>"><?php echo $product['QUANTITY'] ?></span>
                                <button class="qty-btn plus w-25" onclick="<?php echo "increaseQuantity(".$product['ID']. ")"; ?>">+</button>
                            </div>
                            <span id="<?php echo $product['ID']; ?>_quantityError"></span>
                        </div>
                <?php 
                        }
                    }
                ?>
            </div>
        </main>
        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>

        <script>
            function increaseQuantity(prodId){
                let quantity = document.getElementById(prodId+"_quantity").innerHTML;
                try{
                    quantity = parseInt(quantity);
                }
                catch(err){
                    document.getElementById(prodId+"_quantityError").innerHTML = "Quantity should be a number";
                    return;
                }
                if(quantity + 1 > 10){
                    document.getElementById(prodId+"_quantityError").innerHTML = "Quantity cannot be greater than 10";
                    return;
                }
                document.getElementById(prodId+"_quantityError").innerHTML = "";
                quantity++;
                const form = new FormData();
                form.append("prodId", prodId);
                form.append("quantity", quantity);
                fetch("./addToCart.php", {
                    method:"POST",
                    header:{"Content-Type": "application/json"},
                    body: form
                })
                .then(resp => resp.json())
                .then(resp => {
                    if(!resp.success){
                        document.getElementById(prodId+"_quantityError").innerHTML = "Unexpected error occurred";
                    }
                    else{
                        document.getElementById(prodId+"_quantity").innerHTML = resp.quantity;
                    }
                })
            }

            function decreaseQuantity(prodId){
                let quantity = document.getElementById(prodId+"_quantity").innerHTML;
                try{
                    quantity = parseInt(quantity);
                }
                catch(err){
                    document.getElementById(prodId+"_quantityError").innerHTML = "Quantity should be a number";
                    return;
                }
                if(quantity - 1 < 0){
                    document.getElementById(prodId+"_quantityError").innerHTML = "Quantity cannot be less than 0";
                    return;
                }
                document.getElementById(prodId+"_quantityError").innerHTML = "";
                quantity--;
                const form = new FormData();
                form.append("prodId", prodId);
                form.append("quantity", quantity);
                fetch("./addToCart.php", {
                    method:"POST",
                    header:{"Content-Type": "application/json"},
                    body: form
                })
                .then(resp => resp.json())
                .then(resp => {
                    if(!resp){
                        document.getElementById(prodId+"_quantityError").innerHTML = "Unexpected error occurred";
                    }
                    else{
                        if(resp.quantity == 0){
                            window.location.reload();
                            return;
                        }
                        document.getElementById(prodId+"_quantity").innerHTML = quantity;
                    }
                })
            }
        </script>
    </body>
</html>