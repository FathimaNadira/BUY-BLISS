<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../client/index.php");
    exit();
}

include("../config/connect.php");
include("../config/function.php");

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Get product info to delete image file
    $product = getProduct($conn, $product_id);
    
    if ($product) {
        // Delete the image file
        $image_path = "../uploads/" . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Delete from database
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        }
    }
}

header("Location: index.php");
exit;
