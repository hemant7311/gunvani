<?php
session_start();
include "../admin/config/db.php"; // 🔥 IMPORTANT PATH

// LOGIN CHECK
if(!isset($_SESSION['user_id'])){
    header("Location: ../admin/auth/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// SECURE INPUT
$product = mysqli_real_escape_string($conn, $_GET['product']);
$price = (int) $_GET['price'];
$quantity = 1;

// INSERT ORDER
mysqli_query($conn, "INSERT INTO orders (user_id, product_name, price, quantity, status) 
VALUES ('$user_id', '$product', '$price', '$quantity', 'Pending')");

// REDIRECT
header("Location: order_success.php");
exit();
?>