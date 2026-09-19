<?php
session_start();
include "../config/db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM admin WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($user && password_verify($password,$user['password'])){

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    header("Location: ../pages/dashboard.php");

}else{
    echo "Invalid Login";
}
?>