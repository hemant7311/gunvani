<!-- GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* =========================
ROOT
========================= */

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

/* =========================
GLOBAL
========================= */

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
font-family:'Poppins',sans-serif;
}

/* =========================
NAVBAR
========================= */

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

/* CONTAINER */

.ishqana-nav-container{
max-width:1200px;
margin:auto;
display:flex;
align-items:center;
justify-content:space-between;
padding:16px 20px;
}

/* LOGO */

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

/* MENU */

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

/* UNDERLINE ONLY FOR NORMAL LINKS */
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

/* ADMIN BUTTON */

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

/* TOGGLE */

.ishqana-toggle{
display:none;
font-size:22px;
color:var(--white);
cursor:pointer;
}

/* MOBILE */

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

<!-- =========================
NAVBAR HTML
========================= -->

<header class="ishqana-navbar" id="navbar">

<div class="ishqana-nav-container">

<!-- LOGO -->
<div class="ishqana-logo">
<i class="fa-solid fa-cookie-bite"></i>
<span>Ishqana</span>
</div>

<!-- MENU -->
<nav class="ishqana-menu" id="ishqanaMenu">
<a href="index.php">Home</a>
<a href="product.php">Products</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
<a href="auth.php" class="ishqana-admin-btn">User Login</a>
</nav>

<!-- TOGGLE -->
<div class="ishqana-toggle" id="ishqanaToggle">
<i class="fa-solid fa-bars"></i>
</div>

</div>
</header>

<!-- =========================
JS
========================= -->

<script>

// MOBILE TOGGLE
const toggleBtn = document.getElementById("ishqanaToggle")
const menu = document.getElementById("ishqanaMenu")

toggleBtn.onclick = () => {
menu.classList.toggle("active")
}

// SCROLL SHADOW
window.addEventListener("scroll",()=>{
document.getElementById("navbar").classList.toggle("scrolled",window.scrollY>50)
})

</script>