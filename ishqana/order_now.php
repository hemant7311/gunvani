<?php
session_start();
include "admin/config/db.php";
require("razorpay-php/Razorpay.php");

use Razorpay\Api\Api;

// 🔐 LOGIN CHECK
if(!isset($_SESSION['user_id'])){
    header("Location:auth.php");
    exit();
}

// 🛒 GET DATA
$product = $_POST['pname'];
$price = (int) $_POST['pprice'];
$quantity = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

$total = $price * $quantity;

// 👉 SESSION SAVE
$_SESSION['order'] = [
    "product" => $product,
    "price" => $price,
    "qty" => $quantity,
    "total" => $total
];

// 🔑 RAZORPAY KEYS
$keyId = "rzp_test_SWtW9YnwpIM5BY";
$keySecret = "MAgDK40tJga4tgAvZSLHb5gI";

$api = new Api($keyId, $keySecret);

// 🔥 ORDER CREATE (IMPORTANT)
$order = $api->order->create([
    'receipt' => 'order_' . time(),
    'amount' => $total * 100,
    'currency' => 'INR'
]);

$razorpay_order_id = $order['id'];
?>

<h2>Processing Payment...</h2>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
var options = {
    "key": "<?php echo $keyId; ?>",
    "amount": "<?php echo $total * 100; ?>",
    "currency": "INR",
    "name": "Cake Shop",
    "description": "<?php echo $product; ?>",
    "order_id": "<?php echo $razorpay_order_id; ?>",

    "handler": function (response){

        fetch("verify_payment.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(response)
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === "success"){
                window.location.href = "payment_success.php";
            } else {
                window.location.href = "paymentfailed.php";
            }
        });
    },

    "modal": {
        "ondismiss": function(){
            window.location.href = "paymentfailed.php";
        }
    }
};

var rzp = new Razorpay(options);
rzp.open();
</script>