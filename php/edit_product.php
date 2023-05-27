<?php
    require('../db/dbinit.php');
    session_start();
    if(!isset($_SESSION['isAdmin'])){
        header("Location: login.php");
        return;
    }
    if($_SESSION['isAdmin'] < 0){
        header("Location: products.php");
        return;
    }

    $error = null;
    if(!empty($_GET['id'])){
        $id = prepare_string($dbc, $_GET['id']);
    } else {
        $id = null;
        $error = "<p> Error! Product Id not found.";
    }

    if($error == null){
        $stmt = mysqli_prepare($dbc, "SELECT * FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
                
        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $name = $row['name'];
            $description = $row['description'];
            $quantity = $row['quantity'];
            $price = $row['price'];
            $imageName = $row['imageName'];
        }// else-> incorrect entry in db
    } else {
        echo $error;
    }
 
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/editStyle.css">
        <title>
            Update Product
        </title>
    </head>
    <body>
        <?php
            
            $errors = [];
            $nameErr = $descriptionErr = $quantityErr = $priceErr = "";
            $decimal_validation_regex = "/^\d+(\.\d{1,2})?$/";
            $number_validation_regex = "/^\\d+$/";
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(empty($_POST['id'])){
                    $errors[] = "error";
                    $id = prepare_string($dbc, $_POST['id']);
                }
                else{
                    $id = intval(prepare_string($dbc, $_POST['id']));
                }
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
                    $query = "UPDATE products SET name = ?, description = ?, quantity = ?, price = ?, imageName = ? WHERE  id = ?;";
                    $stmt = mysqli_prepare($dbc, $query);
                    mysqli_stmt_bind_param(
                        $stmt,
                        'ssidsi',
                        $name,
                        $description,
                        $quantity,
                        $price,
                        $imageName,
                        $id
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
            include_once('./header.php')
        ?>

        

        <main>
            <div class="addProductsButtonContainer">
                <a href="./details.php"> <input id = "addNewProduct" type="button" value="Click to add a new product!" /></a>
            </div>
                    
            <form class="form" method="post"  id="update_details_form">
                <div class="subtitle">Edit and Save Details</div>

                <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">

                <div class="input-container ic2">
                    <input type="text" class="input" id="name" name="name" value="<?php echo $name; ?>"/>
                    <label for="name" class="placeholder">Name</label>
                    <span class="error"><?php echo $nameErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="description" name="description" value="<?php echo $description; ?>"/>
                    <label for="description" class="placeholder">Description</label>
                    <span class="error"><?php echo $descriptionErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="quantity" name="quantity" value="<?php echo $quantity; ?>"/>
                    <label for="quantity" class="placeholder">Quantity</label>
                    <span class="error"><?php echo $quantityErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="price" name="price" value="<?php echo $price; ?>"/>
                    <label for="price" class="placeholder">Price($)</label>
                    <span class="error"><?php echo $priceErr;?></span>
                </div>

                <div class="input-container ic2">
                    <input type="text" class="input" id="imageName" name="imageName" value="<?php echo $imageName; ?>"/>
                    <label for="imageName" class="placeholder">Image Name</label>
                </div>

                <button type="submit" class="submitButton">Update</button>
            </form>
        </main>

        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>
    </body>
</html>
