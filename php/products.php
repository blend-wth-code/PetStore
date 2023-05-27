<?php
    require('../db/dbinit.php');
    session_start();
    if(!isset($_SESSION['isAdmin'])){
        header("Location: login.php");
        session_destroy();
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/productsStyle.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <title>
            Add Product
        </title>
    </head>
    <body>
       <?php 
            include_once('./header.php');
            $search = "";
            if($_SERVER["REQUEST_METHOD"] == 'POST'){
                $search = prepare_string($dbc, $_POST['search']);
                if(isset($search)){
                    $query = 'SELECT * FROM products WHERE NAME LIKE ? OR DESCRIPTION LIKE ?';
                    // Prepare the statement
                    $search = prepare_string($dbc, $search);
                    if(!$search) {
                        $query = 'SELECT * FROM products;'; 
                        $results = @mysqli_query($dbc,$query); 
                    }
                    else{
                        $stmt = mysqli_prepare($dbc, $query);
                        $search_param = "%$search%";
                        mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);

                        // Execute the statement and get the result set
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        // Store the results in an array
                        $results = array();
                        while ($row = mysqli_fetch_assoc($result)) {
                            $results[] = $row;
                        }   
                    }
                    
                }
            }
            else{
                $query = 'SELECT * FROM products;'; // replace with paramertized query using mysqli_stmt_bind_param for asynchronous work task
                $results = @mysqli_query($dbc,$query); // print_r($results);
            }
       
       ?>
        <main>
            <div class="searchContainer">
                <form method="POST">
                    <input type="text" id="search" name="search" value="<?php echo $search ?>" placeholder="Search for product...">
                    <input type="submit" value="Search" id="searchBtn">
                </form>
            </div>
            <div class="productGridContainer">
                <?php foreach ($results as $product) { 
                    $quantity = 0;
                    if (isset($_SESSION['email'])) {
                        $query = "SELECT quantity FROM orders WHERE prod_id = ? AND email = ?";
                        $stmt = mysqli_prepare($dbc, $query);
                        mysqli_stmt_bind_param($stmt, 'is', $product['id'], $_SESSION['email']);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $quantity);
                        mysqli_stmt_fetch($stmt);
                        mysqli_stmt_close($stmt);
                    }   ?>
                    <div class="productSection">
                        <img src="../images/<?php echo $product['imageName']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p class="price"><?php echo '$ ' . $product['price']; ?></p>
                        <?php if($_SESSION['isAdmin'] < 0) { ?>
                            <div class="productQuantity">
                                <div class="quantityControl">
                                    <label for="<?php echo $product['id']; ?>_quantity">Quantity:</label>
                                    <button type="button" class="quantityBtn minus" onclick="<?php echo "decreaseQuantity(".$product['id']. ")"; ?>">-</button>
                                    <span class="quantityInput" id="<?php echo $product['id']; ?>_quantity"><?php echo $quantity; ?></span>
                                    <button type="button" class="quantityBtn plus" onclick="<?php echo "increaseQuantity(".$product['id']. ")"; ?>">+</button>
                                </div>
                                <span class="text-danger" id="<?php echo $product['id']; ?>_quantityError"></span>
                            </div>
                            <div class="buttonGroup">
                                <a class="goToCartBtn" href="./cart.php">Go to Cart</a>
                            </div>
                        <?php }?>
                    </div>
                <?php } ?>
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
                        document.getElementById(prodId+"_quantity").innerHTML = quantity;
                    }
                })
            }
        </script>
    </body>
</html>