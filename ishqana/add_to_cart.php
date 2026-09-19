<?php
session_start();
include "../admin/config/db.php";

// 🔐 LOGIN CHECK
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 📦 GET DATA
$product = $_GET['product'] ?? '';
$price   = $_GET['price'] ?? 0;
$qty     = $_GET['qty'] ?? 1;
$image   = $_GET['image'] ?? '';

// 🔐 SECURITY
$product = mysqli_real_escape_string($conn, $product);
$image   = mysqli_real_escape_string($conn, $image);
$price   = (int)$price;
$qty     = (int)$qty;

// 🚫 BASIC VALIDATION
if(empty($product) || empty($price)){
    die("Invalid data");
}

// 🛒 CHECK PRODUCT EXISTS
$check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND product='$product'");

if(mysqli_num_rows($check) > 0){

    // 👉 UPDATE (QTY + IMAGE ALSO UPDATE)
    $update = mysqli_query($conn, "UPDATE cart 
    SET quantity = quantity + $qty,
        image = '$image'
    WHERE user_id='$user_id' AND product='$product'");

    if(!$update){
        die("Update Error: " . mysqli_error($conn));
    }

}else{

    // 👉 INSERT
    $insert = mysqli_query($conn, "INSERT INTO cart (user_id, product, price, quantity, image) 
    VALUES ('$user_id','$product','$price','$qty','$image')");

    if(!$insert){
        die("Insert Error: " . mysqli_error($conn));
    }
}

// 👉 REDIRECT
header("Location: checkout.php");
exit();
?>