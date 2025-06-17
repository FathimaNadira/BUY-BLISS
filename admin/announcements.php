<?php
session_start();

include("../config/connect.php");
include("../config/function.php");

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../client/index.php");
    exit();
}

// Include the head
include("../inc/head.php");

// Include the header
include("../inc/header.php");
?>
<link rel="stylesheet" href="../css/dash.css">
<div class="container">
    <!-- Start Welcome -->
    <div class="py-5 lc-block">
        <div class="lc-block">
            <div editable="rich">
                <h2 class="fw-bolder display-5">Welcome Admin</h2><br>
            </div>
        </div>
        <div class="lc-block col-md-8">
            <div editable="rich">
                <p class="lead">Welcome to your dashboard, Admin! This is your central hub for managing users and announcements. Here, you can efficiently oversee user accounts, update information, and ensure smooth operations. Additionally, you have the power to create and distribute announcements to keep your community informed and engaged!
                </p>
            </div>
        </div>
    </div>
    <!-- End Welcome -->

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mt-4">Announcements</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="process_product.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="productName" name="product_name" required>
                                        <label for="productName">Product Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                        <label for="price">Price (RS.)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Electronics">Electronics</option>
                                            <option value="Kitchen Items">Kitchen Items</option>
                                            <option value="Books">Books</option>
                                            <option value="Home">Home Accessories</option>
                                            <option value="Sports">Sports</option>
                                            <option value="Cosmetics">Cosmetics</option>
                                        </select>
                                        <label for="category">Category</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                                        <label for="quantity">Quantity</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="description" name="description" style="height: 100px" required></textarea>
                                        <label for="description">Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="vendor" name="vendor_id" required>
                                            <option value="">Select Vendor</option>
                                            <?php
                                            $vendors = $conn->query("SELECT vendor_id, vendor_name FROM vendor");
                                            while($vendor = $vendors->fetch_assoc()) {
                                                echo "<option value='".$vendor['vendor_id']."'>".$vendor['vendor_name']."</option>";
                                            }
                                            ?>
                                        </select>
                                        <label for="vendor">Vendor</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="awaiting">Awaiting</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form action="update_status.php" method="post">
            <?php
            include("../config/connect.php");

            $sql = "SELECT product.*, vendor.vendor_name FROM product INNER JOIN vendor ON product.vendor_id = vendor.vendor_id";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
            ?>
                <table class="table align-middle mb-0 bg-white">
                    <thead class="bg-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Create Date</th>
                            <th>Seller Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['product_name']; ?>" class="image img-fluid product-thumbnail" style="width: 50px; height: 50px;"></td>
                                <td>
                                    <p style="width: 100px; max-height: 20px; overflow: hidden; margin: 0;"><?php echo $row['product_name']; ?></p>
                                </td>
                                <td>
                                    <p style="width: 200px; max-height: 20px; overflow: hidden; margin: 0;"><?php echo $row['description']; ?></p>
                                </td>
                                <td>"RS."<?php echo $row['price'] ; ?></td>
                                <td>
                                    <select name="product_status[<?php echo $row['product_id']; ?>]" class="form-select">
                                        <option value="awaiting" <?php echo ($row['status'] == 'awaiting') ? 'selected' : ''; ?>>Awaiting</option>
                                        <option value="active" <?php echo ($row['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($row['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td><?php echo $row['create_date']; ?></td>
                                <td><?php echo $row['vendor_name']; ?></td>
                                <td style="width: 200px;">
                                    <button type="submit" name="submit_product" class="btn btn-primary mx-2"><i class="fas fa-save"></i></button>
                                    <a href="product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } else {
                echo "No products found.";
            }
            ?>
        </form>
    </div>
    <br><br><br><br><br><br>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/tiny-slider.js"></script>
<script src="../js/custom.js"></script>