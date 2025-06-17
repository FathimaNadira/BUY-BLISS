<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../client/index.php");
    exit();
}

include("../config/connect.php");

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $vendor_id = $_POST['vendor_id'];
    $status = $_POST['status'];

    // Handle image upload
    $target_dir = "../uploads/";
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO product (product_name, description, price, category, quantity, image, vendor_id, status, create_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsisis", $product_name, $description, $price, $category, $quantity, $new_filename, $vendor_id, $status);
        
        if ($stmt->execute()) {
            header("Location: announcements.php?success=Product added successfully");
        } else {
            header("Location: announcements.php?error=Failed to add product");
        }
    } else {
        header("Location: announcements.php?error=Failed to upload image");
    }
    exit();
}
