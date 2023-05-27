<?php
    require('../db/dbinit.php');
    session_start();
    if(!isset($_SESSION['isAdmin'])){
        header("Location: login.php");
        session_destroy();
        return;
    }
    $query = 'SELECT * FROM products;'; // replace with paramertized query using mysqli_stmt_bind_param for asynchronous work task
    $results = @mysqli_query($dbc,$query); // print_r($results);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/adminStyle.css">
        <title>
            Product Inventory
        </title>
    </head>
    <body>
        <?php 
            include_once('./header.php');
            if($_SESSION['isAdmin'] < 0){
                header("Location: products.php");
                return;
            }
        ?>
        <main>
            <div id="addProductsButtonContainer">
                <a href="./details.php"> <input id = "details_button" type="button" value="Add Products" /></a>
            </div>
            
            <table width="80%">
                <thead>
                    <tr align="left">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price($)</th>
                        <th>Image Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sr_no = 0;
                        while($row = mysqli_fetch_array($results, MYSQLI_ASSOC)){
                        
                            $str_to_print = "";
                            $str_to_print = "<tr> <td>" . prepare_string($dbc, $row['id']) ."</td>";
                            $str_to_print .= ("<td> ". prepare_string($dbc,$row['name']) ."</td>");
                            $str_to_print .= ("<td> ". prepare_string($dbc, $row['description'])."</td>");
                            $str_to_print .= ("<td> " . prepare_string($dbc, $row['quantity']) . "</td>");
                            $str_to_print .= ("<td> " . prepare_string($dbc, $row['price']) . "</td>");
                            $str_to_print .= ("<td> ". prepare_string($dbc, $row['imageName']) . "</td>");
                            $str_to_print .= "<td> <a href='edit_product.php?id=" . prepare_string($dbc, $row['id']) ."'>Edit</a> | <a href='delete_product.php?id=".prepare_string($dbc, $row['id']) ."'>Delete</a> </td> </tr>";
                            echo $str_to_print;
                        }
                    ?>
                </tbody>
            </table>
        </main>

        <footer class="footer">
            <p>@copyright Snoopy's Pet Supplies 2023</p>
        </footer>
                
    </body>
</html>











