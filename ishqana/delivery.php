<style>

.delivery-box{
margin:auto;
background:var(--white);
padding:40px 35px;
display:flex;
align-items:center;
justify-content:space-between;
gap:30px;
border:1px solid var(--border);
box-shadow:var(--shadow-sm);
transition:.3s;
}

.delivery-box:hover{
box-shadow:var(--shadow-md);
}

/* IMAGE */

.delivery-img{
flex:1;
display:flex;
justify-content:center;
align-items:center;
}

.delivery-img img{
width:110px; /* 🔥 reduced for balance */
opacity:0.9;
transition:.3s;
}

.delivery-box:hover .delivery-img img{
transform:scale(1.06);
opacity:1;
}

/* CONTENT */

.delivery-content{
flex:2;
text-align:center;
max-width:520px;
margin:auto;
}

/* TOP TEXT */

.delivery-top{
font-size:13px;
color:var(--primary);
font-weight:600;
margin-bottom:6px;
letter-spacing:0.4px;
}

/* TITLE */

.delivery-title{
font-size:30px; /* 🔥 reduced */
font-weight:700;
color:var(--text-dark);
margin-bottom:8px;
line-height:1.3;
}

/* TEXT */

.delivery-text{
font-size:14px;
color:var(--text-muted);
margin-bottom:16px;
}

/* BUTTON */

.delivery-btn{
display:inline-block;
padding:10px 24px;
border-radius:30px;
background:var(--primary);
color:var(--black);
font-size:13px;
font-weight:600;
text-decoration:none;
transition:.3s;
}

.delivery-btn:hover{
background:var(--primary-hover);
transform:translateY(-2px);
}

/* MOBILE */

@media(max-width:768px){

.delivery-box{
flex-direction:column;
text-align:center;
padding:30px 20px;
gap:20px;
}

.delivery-img img{
width:90px;
}

.delivery-title{
font-size:24px;
}

}

</style>


<section class="delivery-section">

<div class="delivery-box">

<!-- LEFT IMAGE -->
<div class="delivery-img">
<img src="./assets/images/user.webp" alt="Happy customer Ishqana bakery review">
</div>

<!-- TEXT -->
<div class="delivery-content">

<div class="delivery-top">
Fresh & Hygienic Guteta
</div>

<h2 class="delivery-title">
Delivery Straight to Your Doorstep
</h2>

<p class="delivery-text">
Available in Shikohabad & nearby areas. Freshly prepared and safely delivered.
</p>

<a href="product.php" class="delivery-btn">Order Now</a>

</div>

<!-- RIGHT IMAGE -->
<div class="delivery-img">
<img src="./assets/images/cycle.webp" alt="Fast delivery service Ishqana bakery">
</div>

</div>

</section>