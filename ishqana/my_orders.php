<?php
session_start();

$conn = mysqli_connect("localhost", "ishqana", "ishqana@20262026", "ishqana");
if(!$conn){ die("DB Error"); }

if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ✅ ONLY LOGGED USER ORDERS */
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* THEME (UNCHANGED) */
:root{
--primary:#f5b84d;
--primary-soft:#fff3d6;
--bg:#f6f7fb;
--white:#fff;
--border:#eee;
--dark:#1a0d08;
--shadow:0 10px 30px rgba(0,0,0,0.08);
}

*{margin:0;padding:0;box-sizing:border-box;}

body{
font-family:'Poppins',sans-serif;
background:var(--bg);
}

/* HEADER */
.header{
width:100%;
background:black;
position:sticky;
top:0;
z-index:999;
}

.navbar{
max-width:1200px;
margin:auto;
display:flex;
justify-content:space-between;
align-items:center;
padding:14px 20px;
}

.ishqana-logo{
display:flex;
align-items:center;
gap:8px;
font-size:20px;
font-weight:700;
color:var(--primary);
cursor:pointer;
}

.nav-right{
display:flex;
align-items:center;
gap:18px;
}

.nav-link{
color:var(--primary);
text-decoration:none;
font-size:14px;
font-weight:500;
}

/* PROFILE */
.profile-menu{position:relative;}

.profile-btn{
background:var(--primary);
color:#000;
padding:6px 14px;
border-radius:20px;
font-size:13px;
cursor:pointer;
display:flex;
align-items:center;
gap:6px;
}

.dropdown{
position:absolute;
top:45px;
right:0;
background:#fff;
border-radius:10px;
box-shadow:var(--shadow);
width:170px;
display:none;
overflow:hidden;
}

.dropdown a{
display:block;
padding:10px 14px;
text-decoration:none;
color:#333;
border-bottom:1px solid #eee;
}

.dropdown a:hover{background:#f5f5f5;}

.profile-menu.active .dropdown{display:block;}

/* MAIN */
.container{
max-width:1100px;
margin:60px auto;
padding:20px;
}

.card{
background:#fff;
border-radius:16px;
box-shadow:var(--shadow);
padding:25px;
}

.title{
font-size:26px;
margin-bottom:20px;
}

/* TABLE */
.table-wrapper{overflow-x:auto;}

.table{
min-width:800px;
width:100%;
border-collapse:collapse;
}

.table th{
background:var(--primary-soft);
padding:14px;
text-align:left;
}

.table td{
padding:16px;
border-bottom:1px solid var(--border);
}

.table tr:hover{background:#fafafa;}

.product{
display:flex;
align-items:center;
gap:12px;
}

.product img{
width:60px;
height:60px;
border-radius:10px;
object-fit:cover;
}

.price{
color:var(--primary);
font-weight:600;
}

.status{
font-size:13px;
padding:5px 10px;
border-radius:20px;
background:#e6ffed;
color:#28a745;
display:inline-block;
}

/* EMPTY */
.empty{
text-align:center;
padding:40px;
color:#888;
}

/* FOOTER */
.footer{
width:100%;
background:var(--dark);
color:var(--primary);
text-align:center;
padding:30px 20px;
margin-top:60px;
font-size:13px;
}

</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
  <div class="navbar">

    <div class="ishqana-logo" onclick="location.href='index.php'">
      <i class="fa-solid fa-cookie-bite"></i>
      <span>Ishqana</span>
    </div>

    <div class="nav-right">
      <a href="index.php" class="nav-link">Home</a>
      <a href="logout.php" class="nav-link">Logout</a>

      <div class="profile-menu" id="profileMenu">
        <div class="profile-btn" onclick="toggleMenu()">
          <i class="fa-solid fa-user"></i> Profile
        </div>

        <div class="dropdown">
          <a href="profile.php">My Profile</a>
          <a href="my_orders.php">My Orders</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>

    </div>

  </div>
</div>

<!-- MAIN -->
<div class="container">
<div class="card">

<div class="title">My Orders</div>

<?php if($result->num_rows > 0){ ?>

<div class="table-wrapper">

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Product</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td>#<?php echo $row['id']; ?></td>

<td>
<div class="product">
<img src="./assets/images/product1.webp" alt="product">
<span><?php echo $row['product_name']; ?></span>
</div>
</td>

<td class="price">₹<?php echo $row['price']; ?></td>

<td><?php echo $row['quantity']; ?></td>

<td class="price">₹<?php echo $row['total_amount']; ?></td>

<td>
<span class="status"><?php echo $row['status']; ?></span>
</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>

<?php } else { ?>
<div class="empty">No orders found 😔</div>
<?php } ?>

</div>
</div>

<!-- FOOTER -->
<div class="footer">
© 2026 Ishqana Cakes | All Rights Reserved
</div>

<script>
function toggleMenu(){
document.getElementById("profileMenu").classList.toggle("active");
}

window.onclick = function(e){
if(!e.target.closest('.profile-menu')){
document.getElementById("profileMenu").classList.remove("active");
}
}
</script>

</body>
</html>