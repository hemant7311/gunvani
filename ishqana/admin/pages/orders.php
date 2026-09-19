<?php include "../auth/check.php"; ?>
<?php include "../config/db.php"; ?>

<?php

// DELETE
if(isset($_GET['delete'])){
    mysqli_query($conn,"DELETE FROM orders WHERE id=".$_GET['delete']);
}

// UPDATE STATUS
if(isset($_GET['done'])){
    mysqli_query($conn,"UPDATE orders SET status='Delivered' WHERE id=".$_GET['done']);
}

$result = mysqli_query($conn,"SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Orders</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}

body{
display:flex;
background:#f6f2ee;
}

/* CONTENT */
.content{
margin-left:260px;
padding:25px;
width:100%;
}

/* TOP */
.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

.page-title{
font-size:24px;
font-weight:600;
color:#2b0f0f;
}

/* TABLE BOX */
.table-box{
background:#fff;
border-radius:12px;
padding:20px;
overflow-x:auto; /* SCROLL */
}

/* TABLE */
table{
width:100%;
min-width:900px;
border-collapse:collapse;
}

th{
background:#2b0f0f;
color:#fff;
padding:12px;
white-space:nowrap;
}

td{
padding:12px;
border-bottom:1px solid #eee;
white-space:nowrap;
}

.table-img{
width:60px;
height:60px;
border-radius:8px;
object-fit:cover;
}

/* STATUS */
.status{
padding:5px 10px;
border-radius:20px;
font-size:12px;
}

.pending{
background:#fff3cd;
color:#856404;
}

.delivered{
background:#d4edda;
color:#155724;
}

/* BUTTON */
.btn{
padding:6px 12px;
border-radius:6px;
color:#fff;
text-decoration:none;
margin-right:5px;
font-size:13px;
display:inline-block;
}

.done{background:#28a745;}
.delete{background:#dc3545;}

/* MOBILE */
@media(max-width:768px){

.content{
margin-left:0;
padding:15px;
}

.table-box{
overflow-x:auto;
-webkit-overflow-scrolling:touch;
}

}

</style>
</head>

<body>

<?php include "../partials/sidebar.php"; ?>

<div class="content">

<?php include "../partials/navbar.php"; ?>

<div class="top-bar">
    <div class="page-title">📦 Orders</div>
</div>

<div class="table-box">

<table>
<tr>
<th>ID</th>
<th>Image</th>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

<td>#<?php echo $row['id']; ?></td>

<!-- ✅ STATIC IMAGE -->
<td>
<img src="../../assets/images/product1.webp" class="table-img">
</td>

<td><?php echo $row['product_name']; ?></td>

<td><?php echo $row['quantity']; ?></td>

<td>₹<?php echo $row['price']; ?></td>

<td>₹<?php echo $row['total_amount']; ?></td>

<td>
<?php if($row['status']=="Paid"){ ?>
<span class="status delivered">Paid</span>
<?php } else { ?>
<span class="status pending"><?php echo $row['status']; ?></span>
<?php } ?>
</td>

<td>
<a class="btn done" href="?done=<?php echo $row['id']; ?>">Done</a>
<a class="btn delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>