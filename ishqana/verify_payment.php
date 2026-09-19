<?php
session_start();
include "admin/config/db.php";
require("razorpay-php/Razorpay.php");

use Razorpay\Api\Api;

$data = json_decode(file_get_contents("php://input"), true);

$keyId = "rzp_test_SWtW9YnwpIM5BY";
$keySecret = "MAgDK40tJga4tgAvZSLHb5gI";

$api = new Api($keyId, $keySecret);

try {

    // 🔐 VERIFY SIGNATURE
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id' => $data['razorpay_order_id'],
        'razorpay_payment_id' => $data['razorpay_payment_id'],
        'razorpay_signature' => $data['razorpay_signature']
    ]);

    $user_id = $_SESSION['user_id'];
    $order = $_SESSION['order'];

    // ✅ INSERT INTO DB
    mysqli_query($conn, "INSERT INTO orders 
    (user_id, razorpay_order_id, razorpay_payment_id, product_name, quantity, price, total_amount, status)
    VALUES (
        '$user_id',
        '".$data['razorpay_order_id']."',
        '".$data['razorpay_payment_id']."',
        '".$order['product']."',
        '".$order['qty']."',
        '".$order['price']."',
        '".$order['total']."',
        'Paid'
    )");

    unset($_SESSION['order']);

    echo json_encode(["status" => "success"]);

} catch(Exception $e){

    echo json_encode(["status" => "failed"]);
}