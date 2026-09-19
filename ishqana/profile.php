<?php
session_start();

// DB CONNECTION
$conn = mysqli_connect("localhost", "ishqana", "ishqana@20262026", "ishqana");
if(!$conn){ die("DB Error"); }

// LOGIN CHECK
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ SAFE QUERY (LIKE my_orders.php)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// ✅ USER FETCH
if($result->num_rows > 0){
    $user = $result->fetch_assoc();
} else {
    $user = [
        "name" => "User",
        "email" => "-",
        "mobile" => "-",
        "id" => 0
    ];
}

// VALUES
$name = $user['name'];
$email = $user['email'];
$mobile = $user['mobile'];
$id = $user['id'];
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* ===== SAME UI (UNCHANGED) ===== */
:root{
--primary:#f5b84d;
--bg:#f6f7fb;
--dark:#1a0d08;
--shadow:0 10px 30px rgba(0,0,0,0.08);
--border:#eee;
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

/* PROFILE */
.profile-wrapper{
max-width:1100px;
margin:60px auto;
padding:20px;
}

.profile-card{
background:#fff;
border-radius:16px;
box-shadow:var(--shadow);
display:flex;
overflow:hidden;
}

.profile-left{
width:300px;
background:var(--dark);
color:#fff;
padding:40px 20px;
text-align:center;
}

.avatar{
width:100px;
height:100px;
border-radius:50%;
background:var(--primary);
display:flex;
align-items:center;
justify-content:center;
font-size:36px;
margin:auto;
margin-bottom:15px;
}

.profile-right{
flex:1;
padding:40px;
}

.profile-title{
font-size:24px;
margin-bottom:25px;
}

.profile-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
}

.profile-box{
border:1px solid var(--border);
border-radius:10px;
padding:15px;
}

.profile-actions{
margin-top:30px;
display:flex;
gap:15px;
flex-wrap:wrap;
}

.btn{
padding:10px 20px;
border-radius:25px;
text-decoration:none;
font-weight:600;
font-size:14px;
}

.btn-primary{
background:var(--primary);
color:#000;
}

.btn-dark{
background:#000;
color:#fff;
}

.footer{
background:var(--dark);
color:var(--primary);
text-align:center;
padding:25px;
margin-top:60px;
font-size:13px;
}

@media(max-width:768px){
.profile-card{flex-direction:column;}
.profile-left{width:100%;}
.profile-grid{grid-template-columns:1fr;}
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

<!-- PROFILE -->
<div class="profile-wrapper">
<div class="profile-card">

<div class="profile-left">
<div class="avatar"><?php echo strtoupper(substr($name,0,1)); ?></div>
<h3><?php echo $name; ?></h3>
<p><?php echo $email; ?></p>
</div>

<div class="profile-right">
<div class="profile-title">My Profile</div>

<div class="profile-grid">
<div class="profile-box"><strong>Name</strong> - <?php echo $name; ?></div>
<div class="profile-box"><strong>Email</strong> - <?php echo $email; ?></div>
<div class="profile-box"><strong>Mobile</strong> - <?php echo $mobile; ?></div>
<div class="profile-box"><strong>ID</strong> - <?php echo $id; ?></div>
</div>

<div class="profile-actions">
<a href="my_orders.php" class="btn btn-primary">My Orders</a>
<a href="logout.php" class="btn btn-dark">Logout</a>
</div>

</div>

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