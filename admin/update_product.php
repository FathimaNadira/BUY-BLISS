<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../client/index.php");
    exit();
}

include("../config/connect.php");
include("../config/function.php");

$message = '';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $product = getProduct($conn, $product_id);

    if (!$product) {
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    // Handle file upload if new image is provided
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update with new image
            $sql = "UPDATE product SET product_name=?, description=?, price=?, category=?, quantity=?, image=? WHERE product_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsssi", $product_name, $description, $price, $category, $quantity, $new_filename, $product_id);
        }
    } else {
        // Update without changing image
        $sql = "UPDATE product SET product_name=?, description=?, price=?, category=?, quantity=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $product_name, $description, $price, $category, $quantity, $product_id);
    }

    if ($stmt->execute()) {
        header("Location: product.php?product_id=" . $product_id);
        exit;
    } else {
        $message = "Error updating product.";
    }
}

include("../inc/head.php");
include("../inc/header.php");
?>

<div class="container py-5">
    <h2 class="mb-4">Update Product</h2>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="product_name" value="<?php echo $product['product_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required><?php echo $product['description']; ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" class="form-control" name="price" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category" required>
                <option value="">Select Category</option>
                <option value="Electronics" <?php echo ($product['category'] == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                <option value="Clothing" <?php echo ($product['category'] == 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                <option value="Books" <?php echo ($product['category'] == 'Books') ? 'selected' : ''; ?>>Books</option>
                <option value="Home" <?php echo ($product['category'] == 'Home') ? 'selected' : ''; ?>>Home</option>
                <option value="Sports" <?php echo ($product['category'] == 'Sports') ? 'selected' : ''; ?>>Sports</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" class="form-control" name="quantity" value="<?php echo $product['quantity']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Image (optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
