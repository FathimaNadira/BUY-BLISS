<?php
session_start();
// Redirect to login page if the user is not logged in
if (!isset($_SESSION['vendorName'])) {
    header("Location: ../client/login.php");
    exit();
}

// Include functions and database connection
include("../config/connect.php");
include("../config/function.php");

// Fetch products for the logged-in vendor by vendor name
$vendorName = $_SESSION['vendorName'];
$products = getProductsByVendorName($vendorName, $conn);

// Include the head
include("../inc/head.php");

// Include the header
include("../inc/header.php");
?>

<style>
    .product-img {
        height: 400px;
    }

    .btn-danger {
        background-color: #e20026;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .title {
        height: 72px;
        overflow: hidden;
    }
</style>
<link rel="stylesheet" href="../css/dash.css">
<div class="container mb-5">
    <!-- Start Welcome -->
    <div class="py-5 mb-2 lc-block">
        <div class="lc-block">
            <div editable="rich">
                <h2 class="fw-bolder display-5">Welcome Customer</h2>
            </div>
        </div>
        <div class="lc-block col-md-8">
            <div editable="rich">
                <p class="lead">Welcome to your dashboard! This is your space to manage your products, view sales data, and keep track of your business. If you need any assistance or have any questions, feel free to reach out to our support team. We're here to help you succeed!
                </p>
            </div>
        </div>
    </div>
    <!-- End Welcome -->

    <section class="py-2">
        <div class="container">
            <h1 class="mt-4">My Announcements</h1><br><br>
            <div class="row">
                <?php
                // Check if $products is set and not empty before iterating
                if (isset($products) && !empty($products)) {
                    foreach ($products as $product) : ?>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <img src="../uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['product_name']; ?>" class="product-img card-img-top">
                                <div class="card-body">
                                    <h2 class="card-title title"><?php echo $product['product_name']; ?></h2>
                                    <p class="card-text" style="height: 185px;overflow: hidden;"><?php echo $product['description']; ?></p>
                                    <p class="card-text">Price: RS.<?php echo $product['price']; ?></p>
                                    <p class="card-text">Status: <span class="badge bg-<?php echo getStatusColorClass($product['status']); ?>"><?php echo ucfirst($product['status']); ?></span></p>
                                    <br>
                                    <div class="row justify-content-center">
                                        <div class="col-auto">
                                            <!-- See button as an icon -->
                                            <a href="product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Edit button with link to edit.php -->
                                            <a href="edit.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Delete button with form submission -->
                                            <form method="post" action="delete.php">
                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endforeach;
                } else {
                    echo "<p class='col'>No products found.</p>";
                }
                ?>
            </div>
        </div>
    </section>
</div>
<br><br>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/tiny-slider.js"></script>
<script src="../js/custom.js"></script>
