<style>

/* =========================
SINGLE PRODUCT (PREMIUM)
========================= */

.single-product{
padding:100px 20px;
background:radial-gradient(circle at top,var(--bg-soft),var(--bg-light));
}

/* CARD */

.product-box{
max-width:1050px;
margin:auto;
display:flex;
align-items:center;
gap:60px;
padding:40px;
border-radius:26px;
background:linear-gradient(145deg,#ffffff,#fff8ef);
box-shadow:var(--shadow-lg);
transition:.3s;
}

.product-box:hover{
transform:translateY(-5px);
}

/* IMAGE */

.product-image{
flex:1;
display:flex;
justify-content:center;
position:relative;
}

.product-image img{
max-width:480px;
width:100%;
border-radius:18px;
filter:drop-shadow(0 20px 30px rgba(0,0,0,0.25));
transition:.3s;
}

.product-box:hover .product-image img{
transform:scale(1.05);
}

/* BADGE */

.badge{
position:absolute;
top:-10px;
left:50%;
transform:translateX(-50%);
background:var(--primary);
color:var(--black);
padding:6px 16px;
border-radius:20px;
font-size:11px;
font-weight:600;
box-shadow:var(--shadow-sm);
}

/* CONTENT */

.product-content{
flex:1;
}

/* TITLE */

.product-content h2{
font-size:36px;
margin-bottom:10px;
color:var(--text-dark);
}

.product-content h2 span{
color:var(--primary);
}

/* DESC */

.product-content p{
font-size:15px;
margin-bottom:18px;
color:var(--text-muted);
line-height:1.6;
}

/* FEATURES */

.product-features{
margin-bottom:20px;
padding-left:0;
}

.product-features li{
list-style:none;
font-size:14px;
margin-bottom:8px;
color:var(--text-dark);
}

/* PRICE */

.price{
font-size:30px;
font-weight:700;
color:var(--primary);
margin-bottom:18px;
}

/* BUTTON */

.btn{
display:inline-block;
padding:12px 30px;
font-size:14px;
border-radius:30px;
background:var(--primary);
color:var(--black);
text-decoration:none;
font-weight:600;
transition:.3s;
box-shadow:0 10px 25px rgba(245,184,77,0.3);
}

.btn:hover{
background:var(--primary-hover);
transform:translateY(-3px);
}

/* MOBILE */

@media(max-width:768px){

.single-product{
padding:80px 20px;
}

.product-box{
flex-direction:column;
text-align:center;
padding:25px 15px;
gap:25px;
}

.product-image img{
max-width:220px;
}

.product-content h2{
font-size:26px;
}

.price{
font-size:22px;
}

}

</style>


<section class="single-product" id="abc">

<div class="product-box">

<!-- IMAGE -->
<div class="product-image">
<img src="https://i0.wp.com/aartimadan.com/wp-content/uploads/2024/10/Meethi-Mathri.jpg?fit=800,449&ssl=1">
</div>

<!-- CONTENT -->
<div class="product-content">

<h2>Ishqana <span>Guteta</span></h2>

<p>
Crispy traditional Indian snack made with authentic desi recipe.
Perfect for tea time and family gatherings.
</p>

<ul class="product-features">
<li>✔ Premium Quality Ingredients</li>
<li>✔ Fresh & Hygienic Preparation</li>
<li>✔ Perfect Tea-Time Snack</li>
<li>✔ Authentic Desi Taste</li>
</ul>

<div class="price">₹99 / 250g</div>

<a href="checkout.php" class="btn">Order Now</a>

</div>

</div>

</section>