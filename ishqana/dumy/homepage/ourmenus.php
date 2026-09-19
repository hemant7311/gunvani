<style>

/* =========================
PRODUCT SECTION
========================= */

.menu-section{
padding:90px 20px;
background:var(--bg-light);
}

/* CONTAINER */

.menu-container{
max-width:1200px;
margin:auto;
text-align:center;
}

/* TITLE */

.menu-title{
font-size:42px;
font-weight:700;
margin-bottom:10px;
color:var(--text-dark);
}

.menu-title span{
color:var(--primary);
}

.menu-subtitle{
margin-bottom:60px;
font-size:16px;
color:var(--text-light);
max-width:600px;
margin-left:auto;
margin-right:auto;
}

/* GRID */

.menu-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:28px;
}

/* CARD */

.menu-card{
background:var(--card-bg);
border-radius:18px;
padding:26px 20px;
box-shadow:var(--shadow-sm);
border:1px solid var(--border);
transition:.35s;
position:relative;
overflow:hidden;
}

.menu-card:hover{
transform:translateY(-8px);
box-shadow:var(--shadow-lg);
}

/* IMAGE */

.menu-card img{
width:100%;
max-width:150px;
margin:auto;
display:block;
margin-bottom:18px;
transition:.4s;
}

.menu-card:hover img{
transform:scale(1.1);
}

/* TITLE */

.menu-card h3{
font-size:19px;
margin-bottom:6px;
color:var(--text-dark);
}

/* DESCRIPTION */

.menu-desc{
font-size:13px;
color:var(--text-muted);
margin-bottom:10px;
}

/* PRICE */

.price{
color:var(--primary);
font-weight:700;
margin-bottom:16px;
font-size:17px;
}

/* BUTTON */

.menu-btn{
background:var(--primary);
color:var(--black);
padding:10px 24px;
border-radius:30px;
text-decoration:none;
font-size:14px;
font-weight:600;
transition:.3s;
display:inline-block;
box-shadow:var(--shadow-sm);
}

.menu-btn:hover{
background:var(--primary-hover);
transform:translateY(-2px);
}

/* BADGE */

.badge{
position:absolute;
top:12px;
left:12px;
background:var(--primary);
color:var(--black);
font-size:11px;
padding:4px 10px;
border-radius:20px;
font-weight:600;
}

/* MOBILE */

@media(max-width:768px){

.menu-title{
font-size:30px;
}

.menu-subtitle{
font-size:14px;
margin-bottom:40px;
}

.menu-card{
padding:22px 16px;
}

}

</style>



<section class="menu-section">

<div class="menu-container">

<h2 class="menu-title">
Our <span>Special Guteta</span>
</h2>

<p class="menu-subtitle">
Crispy traditional Indian snack made with premium ingredients and authentic desi taste.
</p>


<div class="menu-grid">

<!-- PRODUCT 1 -->
<div class="menu-card">
<span class="badge">New</span>
<img src="./aa.png" alt="Guteta">
<h3>Classic Guteta</h3>
<p class="menu-desc">Crispy & traditional desi snack</p>
<p class="price">₹120 / 250g</p>
<a href="#" class="menu-btn">Order Now</a>
</div>

<!-- PRODUCT 2 -->
<div class="menu-card">
<span class="badge">New</span>
<img src="./aa.png" alt="Guteta">
<h3>Sweet Guteta</h3>
<p class="menu-desc">Light sweet flavor with crunch</p>
<p class="price">₹140 / 250g</p>
<a href="#" class="menu-btn">Order Now</a>
</div>

<!-- PRODUCT 3 -->
<div class="menu-card">
<span class="badge">New</span>
<img src="./aa.png" alt="Guteta">
<h3>Premium Mix Guteta</h3>
<p class="menu-desc">Cashew & coconut enriched taste</p>
<p class="price">₹160 / 250g</p>
<a href="#" class="menu-btn">Order Now</a>
</div>

<!-- PRODUCT 4 -->
<div class="menu-card">
<span class="badge">New</span>
<img src="./aa.png" alt="Guteta">
<h3>Special Desi Guteta</h3>
<p class="menu-desc">Rich flavor with extra crunch</p>
<p class="price">₹150 / 250g</p>
<a href="#" class="menu-btn">Order Now</a>
</div>

</div>

</div>

</section>