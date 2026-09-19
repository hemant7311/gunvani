<style>

/* =========================
PRODUCT SECTION (PRO)
========================= */

.single-product{
padding:60px 20px;
background:var(--white);
}

.section-title{
text-align:center;
margin-bottom:35px;
}

.section-title h2{
font-size:28px;
color:var(--text-dark);
font-weight:700;
}

.section-title span{
color:var(--primary);
}

.product-wrapper{
max-width:1100px;
margin:auto;
display:flex;
align-items:center;
justify-content:space-between;
gap:60px;
flex-wrap:wrap;
}

.product-image{
flex:1;
display:flex;
justify-content:center;
min-width:280px;
}

.image-box{
width:100%;
max-width:360px;
height:260px;
border-radius:14px;
overflow:hidden;
border:1px solid var(--border);
box-shadow:var(--shadow-sm);
}

.image-box img{
width:100%;
height:100%;
object-fit:cover;
transition:.3s;
}

.image-box:hover img{
transform:scale(1.04);
}

.product-content{
flex:1;
min-width:280px;
}

.product-content h1{
font-size:28px;
font-weight:700;
color:var(--text-dark);
margin-bottom:6px;
}

.product-content h1 span{
color:var(--primary);
}

.product-content p{
font-size:14px;
color:var(--text-muted);
margin-bottom:18px;
line-height:1.6;
}

.features{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:10px;
margin-bottom:18px;
}

.feature-box{
display:flex;
align-items:center;
gap:8px;
padding:8px 10px;
border:1px solid var(--border);
border-radius:6px;
font-size:13px;
background:var(--bg-soft);
}

.feature-box i{
color:var(--primary);
}

.price-row{
display:flex;
align-items:center;
gap:12px;
margin-bottom:18px;
}

.price{
font-size:28px;
font-weight:800;
color:var(--primary);
}

.old-price{
text-decoration:line-through;
color:var(--text-light);
font-size:13px;
}

.btn{
display:inline-block;
padding:12px 28px;
border-radius:30px;
background:var(--primary);
color:var(--black);
font-weight:600;
text-decoration:none;
transition:.3s;
font-size:14px;
}

.btn:hover{
background:var(--primary-hover);
transform:translateY(-2px);
}

.trust{
margin-top:12px;
font-size:12px;
color:var(--text-light);
display:flex;
gap:15px;
flex-wrap:wrap;
}

.trust i{
color:var(--primary);
}

/* =========================
FORM IMPROVEMENT (SAFE)
========================= */

form{
display:flex;
align-items:center;
gap:12px;
margin-top:10px;
}

form input{
width:70px;
height:40px;
border-radius:6px;
border:1px solid var(--border);
text-align:center;
font-size:14px;
font-weight:600;
outline:none;
transition:0.3s;
background:var(--white);
color:var(--text-dark);
}

form input:focus{
border-color:var(--primary);
box-shadow:0 0 0 2px rgba(0,0,0,0.05);
}

form input[type="submit"]{
width:auto;
padding:0 20px;
height:40px;
border:none;
border-radius:30px;
background:var(--primary);
color:var(--black);
font-weight:600;
font-size:14px;
cursor:pointer;
transition:0.3s;
}

form input[type="submit"]:hover{
background:var(--primary-hover);
transform:translateY(-2px);
}

form input[type="submit"]:active{
transform:scale(0.97);
}

/* MOBILE */

@media(max-width:768px){

.product-wrapper{
flex-direction:column;
text-align:center;
}

.image-box{
height:220px;
}

.product-content h1{
font-size:22px;
}

.features{
grid-template-columns:1fr;
}

.price-row{
justify-content:center;
}

.trust{
justify-content:center;
}

form{
flex-direction:column;
gap:10px;
}

form input{
width:100%;
}

form input[type="submit"]{
width:100%;
}

}

</style>


<section class="single-product">

<div class="section-title">
<h2>Our <span>Special Product</span></h2>
</div>

<div class="product-wrapper">

<!-- IMAGE -->
<div class="product-image">
<div class="image-box">
<img src="./assets/images/product1.webp" alt="Fresh homemade cookies Ishqana premium bakery product">
</div>
</div>

<!-- CONTENT -->
<div class="product-content">

<h1>Ishqana <span>Guteta</span></h1>

<p>
Authentic crispy Indian snack made with traditional desi recipe.
Perfect for tea-time and daily cravings.
</p>

<div class="features">
<div class="feature-box"><i class="bi bi-check-circle"></i> Premium Ingredients</div>
<div class="feature-box"><i class="bi bi-shield-check"></i> Hygienic Preparation</div>
<div class="feature-box"><i class="bi bi-stars"></i> Crunchy & Fresh</div>
<div class="feature-box"><i class="bi bi-heart"></i> Homemade Taste</div>
</div>

<div class="price-row">
<div class="price">₹99</div>
<div class="old-price">₹149</div>
</div>

<!-- ORDER FORM -->
<form action="order_now.php" method="post">
    <input type="hidden" name="pname" value="Ishqana Guteta">
    <input type="hidden" name="pprice" value="99">

    <!-- FIXED INPUT -->
    <input type="number" name="qty" value="1" min="1">

    <input type="submit" value="Order Now">
</form>

</div>

</div>

</section>