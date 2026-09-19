<style>

/* ======================
PRODUCT SECTION (PRO)
====================== */

.products-section{
padding:100px 20px;
background:linear-gradient(135deg,var(--chocolate-light),var(--chocolate));
position:relative;
overflow:hidden;
}

/* CONTAINER */

.products-container{
max-width:1200px;
margin:auto;
}

/* ======================
HEADER (BETTER ALIGNMENT)
====================== */

.products-header{
text-align:center;

margin:0 auto 70px auto;
}

.products-title{
font-size:44px;
font-weight:700;
color:var(--white);
margin-bottom:12px;
}

.products-title span{
color:var(--primary);
}

.products-subtitle{
font-size:16px;
color:var(--primary-soft);
line-height:1.6;
}

/* ======================
GRID
====================== */

.products-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(270px,1fr));
gap:30px;
}

/* ======================
CARD
====================== */

.product-card{
background:var(--white);
border-radius:20px;
overflow:hidden;
box-shadow:var(--shadow-md);
transition:.4s;
display:flex;
flex-direction:column;
height:100%;
}

.product-card:hover{
transform:translateY(-10px);
box-shadow:var(--shadow-lg);
}

/* IMAGE AREA */

.product-image-wrapper{
background:var(--bg-soft);
padding:25px;
display:flex;
align-items:center;
justify-content:center;
height:200px;
}

.product-img{
max-width:160px;
transition:.3s;
}

.product-card:hover .product-img{
transform:scale(1.1);
}

/* ======================
CONTENT
====================== */

.product-content{
padding:22px;
display:flex;
flex-direction:column;
flex-grow:1;
}

/* TITLE */

.product-title{
font-size:20px;
font-weight:600;
margin-bottom:6px;
color:var(--text-dark);
}

/* DESC */

.product-desc{
font-size:14px;
color:var(--text-muted);
margin-bottom:18px;
line-height:1.5;
flex-grow:1;
}

/* ======================
BOTTOM ALIGN FIX
====================== */

.product-bottom{
display:flex;
align-items:center;
justify-content:space-between;
margin-top:auto; /* 🔥 FIX ALIGNMENT */
}

/* PRICE */

.product-price{
font-size:20px;
font-weight:700;
color:var(--primary);
}

/* BUTTON */

.product-btn{
background:var(--primary);
color:var(--black);
border:none;
padding:10px 24px;
border-radius:30px;
font-size:14px;
font-weight:600;
cursor:pointer;
transition:.3s;
}

.product-btn:hover{
background:var(--primary-hover);
transform:translateY(-2px);
}

/* ======================
MOBILE
====================== */

@media(max-width:768px){

.products-section{
padding:80px 20px;
}

.products-title{
font-size:32px;
}

.products-subtitle{
font-size:14px;
}

}

</style>



<section class="products-section">

<div class="products-container">

<!-- HEADER -->
<div class="products-header">

<h2 class="products-title">
Our <span>Special Guteta</span>
</h2>

<p class="products-subtitle">
Crispy traditional Indian snack made with premium ingredients and authentic desi taste.
</p>

</div>


<!-- GRID -->
<div class="products-grid">


<!-- CARD 1 -->
<div class="product-card">

<div class="product-image-wrapper">
<img src="./aa.png" class="product-img">
</div>

<div class="product-content">

<h3 class="product-title">Classic Guteta</h3>

<p class="product-desc">
Crispy traditional snack made with wheat, sesame & desi flavor.
</p>

<div class="product-bottom">
<span class="product-price">₹120 / 250g</span>
<button class="product-btn">Order</button>
</div>

</div>

</div>


<!-- CARD 2 -->
<div class="product-card">

<div class="product-image-wrapper">
<img src="./aa.png" class="product-img">
</div>

<div class="product-content">

<h3 class="product-title">Sweet Guteta</h3>

<p class="product-desc">
Light sweet taste with crunchy texture and cardamom flavor.
</p>

<div class="product-bottom">
<span class="product-price">₹140 / 250g</span>
<button class="product-btn">Order</button>
</div>

</div>

</div>


<!-- CARD 3 -->
<div class="product-card">

<div class="product-image-wrapper">
<img src="./aa.png" class="product-img">
</div>

<div class="product-content">

<h3 class="product-title">Premium Mix Guteta</h3>

<p class="product-desc">
Rich taste with coconut & cashew mix for extra crunch.
</p>

<div class="product-bottom">
<span class="product-price">₹160 / 250g</span>
<button class="product-btn">Order</button>
</div>

</div>

</div>


</div>

</div>

</section>