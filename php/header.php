<div class="header">
    <?php
        if(!isset($_SESSION['isAdmin'])){
            return;
        }
    ?>
    <header class="headerTitle">
        Snoopy's Pet Supplies
    </header>

    <nav class="nav">
    <ul>
        <li><a href="./products.php">Products</a></li>
        <?php if($_SESSION['isAdmin'] < 0){ ?>
            <li><a href="./cart.php">Cart</a></li>
            <li><a href="./checkout.php">Checkout</a></li>
        <?php } else {?>
            <li><a href="./admin.php">Admin</a></li>
        <?php } ?>
        <li><a href="./logout.php">Logout</a></li>
    </ul>
    </nav>
</div>