<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/detailsStyle.css">
        <title>
            Add Product
        </title>
    </head>
    <body>
        <?php 
            session_start();
            include_once('./header.php');
        ?>
        <main>
            <?php
                require('../db/dbinit.php');
                if(!isset($_SESSION['isAdmin'])){
                    header("Location: login.php");
                    session_destroy();
                    return;
                }
                if($_SESSION['isAdmin'] < 0){
                    header("Location: products.php");
                    return;
                }
                $errors = [];
                $name = $description = $imageName = "";
                $quantity = $price = "";
                $nameErr = $descriptionErr = $quantityErr = $priceErr = "";
                $decimal_validation_regex = "/^\d+(\.\d{1,2})?$/";
                $number_validation_regex = "/^\\d+$/";
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    
                    if(empty($_POST['name'])){
                        $nameErr = "* Name is required";
                        $errors[] = "error";
                        $name = prepare_string($dbc, $_POST['name']);  
                    }
                    else{
                        $name = prepare_string($dbc, $_POST['name']); 
                    }
                    if(empty($_POST['description'])){
                        $descriptionErr = "* Description is required";
                        $errors[] = "error";
                        $description = prepare_string($dbc, $_POST['description']);  
                    }
                    else{
                        $description = prepare_string($dbc, $_POST['description']);  
                    }
                
                    if(!empty($_POST['quantity'])){
                        $quantity = prepare_string($dbc, $_POST['quantity']); 
                        $quantity = intval($quantity);
                        if(empty($quantity) && ($quantity >= 0) && (preg_match($number_validation_regex,$quantity))){
                            $quantityErr = "* Quantity > 0 is required";
                            $errors[] = "error";
                        }
                    } 
                    else{
                        $quantity = prepare_string($dbc, $_POST['quantity']); 
                        $quantityErr = "* Quantity is a number";
                        $errors[] = "error";
                    }
                    
                    if(!empty($_POST['price'])){
                        $price = prepare_string($dbc, $_POST['price']);
                        $price = intval($price);
                        if(empty($price && ($price >= 0)) && (preg_match($decimal_validation_regex,$price))){
                            $priceErr = "* Price > 0 is required";
                            $errors[] = "error";
                        }
                    }  
                    else{
                        $price = prepare_string($dbc, $_POST['price']);
                        $priceErr = "* Price is a number";
                        $errors[] = "error";
                    }
                    if(empty($_POST['imageName'])){
                        $imageName = "Default Image.jpg";
                    }
                    else{
                        $imageName = prepare_string($dbc, $_POST['imageName']);  
                    }

                    if(count($errors) == 0){
                        $query = "INSERT INTO products(name , description, quantity, price, imageName) VALUES (?,?,?,?,?)";
                        $stmt = mysqli_prepare($dbc, $query);
                        mysqli_stmt_bind_param(
                            $stmt,
                            'ssids',
                            $name,
                            $description,
                            $quantity,
                            $price,
                            $imageName
                        );
                        
                        $result = mysqli_stmt_execute($stmt);
                        
                        if($result){
                            header("Location: admin.php");
                            exit;
                        } else {
                            echo "</br>Some error in Saving the data";
                        }
                        
                    } 
                }
            ?>

            <div id = "viewInventoryButtonContainer">
                <a href="./admin.php"> <input id = "inventory_button" type="button" value="View Inventory" /></a>
            </div>

            <!-- <form class="form" action="addProduct.php" method="post" id="registration_form"> -->
            <form class="form" method="post" id="registration_form">
                <div class="input-container ic2 firstic2" >
                    <input type="text" class="input" id="name" name="name" value= "<?php echo $name;?>"/>
                    <label for="name" class="placeholder">Name</label>
                    <span class="error"><?php echo $nameErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="description" name="description" value= "<?php echo $description;?>"/>
                    <label for="description" class="placeholder">Description</label>
                    <span class="error"><?php echo $descriptionErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="quantity" name="quantity" value= "<?php echo $quantity;?>"/>
                    <label for="quantity" class="placeholder">Quantity</label>
                    <span class="error"><?php echo $quantityErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="price" name="price" value= "<?php echo $price;?>"/>
                    <label for="price" class="placeholder">Price($)</label>
                    <span class="error"><?php echo $priceErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="imageName" name="imageName" value= "<?php echo $imageName;?>"/>
                    <label for="imageName" class="placeholder">Image Name</label>
                </div>

                <button type="submit" class="submitButton">Add to Inventory</button>
            </form>
        </main>

        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>
    </body>
</html>