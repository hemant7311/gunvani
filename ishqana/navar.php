<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ========================= ROOT ========================= */
:root{
--white:#ffffff;
--black:#000000;

--primary:#f5b84d;
--primary-hover:#ffcc66;
--primary-soft:#fff3d6;

--chocolate:#1a0d08;
--chocolate-light:#4d1d0a;
--chocolate-dark:#120804;

--text-dark:#1a0d08;
--text-light:#888888;
--text-muted:#666666;

--bg-light:#f9f9f9;
--bg-soft:#fff8ef;
--bg-dark:#1a0d08;

--card-bg:#ffffff;
--card-hover:#fff8ef;

--border:#eeeeee;
--border-dark:rgba(0,0,0,0.08);

--shadow-sm:0 5px 15px rgba(0,0,0,0.05);
--shadow-md:0 10px 30px rgba(0,0,0,0.08);
--shadow-lg:0 20px 60px rgba(0,0,0,0.12);
}

/* GLOBAL */
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Poppins',sans-serif;}

/* NAVBAR */
.ishqana-navbar{
width:100%;
position:sticky;
top:0;
z-index:999;
background:rgba(26,13,8,0.95);
backdrop-filter:blur(10px);
border-bottom:1px solid rgba(255,255,255,0.05);
transition:0.3s;
}

.ishqana-navbar.scrolled{
box-shadow:var(--shadow-md);
}

.ishqana-nav-container{
max-width:1200px;
margin:auto;
display:flex;
align-items:center;
justify-content:space-between;
padding:16px 20px;
}

.ishqana-logo{
display:flex;
align-items:center;
gap:10px;
font-size:24px;
font-weight:600;
color:var(--primary);
letter-spacing:1px;
}

.ishqana-logo i{
font-size:26px;
}

.ishqana-menu{
display:flex;
align-items:center;
gap:28px;
}

.ishqana-menu a{
position:relative;
text-decoration:none;
color:var(--white);
font-size:15px;
font-weight:500;
transition:0.3s;
}

.ishqana-menu a:not(.ishqana-admin-btn)::after{
content:"";
position:absolute;
bottom:-6px;
left:0;
width:0%;
height:2px;
background:var(--primary);
transition:0.3s;
}

.ishqana-menu a:not(.ishqana-admin-btn):hover::after{
width:100%;
}

.ishqana-menu a:hover{
color:var(--primary);
}

/* BUTTON */
.ishqana-admin-btn{
padding:9px 22px;
background:var(--primary);
border-radius:30px;
color:var(--black);
font-size:14px;
font-weight:600;
text-decoration:none;
transition:0.3s;
box-shadow:var(--shadow-sm);
}

.ishqana-admin-btn:hover{
background:var(--primary-hover);
transform:translateY(-2px);
color:var(--black) !important;
}

/* 🔥 PROFILE DROPDOWN (UI MATCHING) */
.profile-box{
position:relative;
}

.profile-btn{
padding:9px 22px;
background:var(--primary);
border-radius:30px;
color:var(--black);
font-size:14px;
font-weight:600;
cursor:pointer;
box-shadow:var(--shadow-sm);
}

.profile-dropdown{
position:absolute;
top:45px;
right:0;
background:var(--white);
border-radius:10px;
box-shadow:var(--shadow-md);
width:170px;
display:none;
overflow:hidden;
}

.profile-dropdown a{
display:block;
padding:12px;
color:var(--text-dark);
text-decoration:none;
font-size:14px;
border-bottom:1px solid var(--border);
}

.profile-dropdown a:hover{
background:var(--primary-soft);
}

/* MOBILE */
.ishqana-toggle{
display:none;
font-size:22px;
color:var(--white);
cursor:pointer;
}

@media(max-width:768px){
.ishqana-menu{
position:absolute;
top:70px;
left:0;
width:100%;
background:var(--chocolate);
flex-direction:column;
align-items:center;
gap:25px;
padding:30px 0;
transform:translateY(-150%);
transition:0.4s;
box-shadow:var(--shadow-md);
}

.ishqana-menu.active{
transform:translateY(0);
}

.ishqana-toggle{
display:block;
}
}

</style>

<header class="ishqana-navbar" id="navbar">

<div class="ishqana-nav-container">

<!-- LOGO -->
<a href="index.php" class="ishqana-logo" style="text-decoration:none;  display:flex; align-items:center; gap:8px;">
  <i class="fa-solid fa-cookie-bite"></i>
  <span>Ishqana</span>
</a>
<!-- MENU -->
<nav class="ishqana-menu" id="ishqanaMenu">

<a href="index.php">Home</a>
<a href="product.php">Products</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>

<?php if(isset($_SESSION['user_id'])){ ?>

<!-- ✅ PROFILE SAME STYLE BUTTON -->
<div class="profile-box">

<div class="profile-btn" onclick="toggleProfile()">
<i class="fa fa-user"></i> Profile
</div>

<div class="profile-dropdown" id="profileDropdown">
<a href="profile.php">My Profile</a>
<a href="my_orders.php">My Orders</a>
<a href="logout.php">Logout</a>
</div>

</div>

<?php } else { ?>

<a href="auth.php" class="ishqana-admin-btn">User Login</a>

<?php } ?>

</nav>

<div class="ishqana-toggle" id="ishqanaToggle">
<i class="fa-solid fa-bars"></i>
</div>

</div>
</header>

<script>

// MOBILE TOGGLE
const toggleBtn = document.getElementById("ishqanaToggle")
const menu = document.getElementById("ishqanaMenu")

toggleBtn.onclick = () => {
menu.classList.toggle("active")
}

// PROFILE DROPDOWN
function toggleProfile(){
let drop = document.getElementById("profileDropdown");
drop.style.display = (drop.style.display === "block") ? "none" : "block";
}

// OUTSIDE CLICK CLOSE
window.onclick = function(e){
if(!e.target.closest('.profile-box')){
document.getElementById("profileDropdown").style.display="none";
}
}

// SCROLL SHADOW
window.addEventListener("scroll",()=>{
document.getElementById("navbar").classList.toggle("scrolled",window.scrollY>50)
})

</script>