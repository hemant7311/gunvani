<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

require_once "db.php";

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $stmt = $pdo->prepare("DELETE FROM members WHERE id=?");
  $stmt->execute([$id]);
}

header("Location: members.php");
exit();
?>
