<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>



<style>

/* ================= PRODUCT PAGE ================= */

.product-page{
padding:60px 20px;
background:#fff;
}

.product-container{
max-width:1150px;
margin:auto;
display:flex;
gap:50px;
flex-wrap:wrap;
}

.product-left{
flex:1;
min-width:280px;
}

.main-img{
width:100%;
height:360px;
border-radius:12px;
overflow:hidden;
border:1px solid #eee;
}

.main-img img{
width:100%;
height:100%;
object-fit:cover;
}

.product-right{
flex:1;
min-width:280px;
}

.product-title{
font-size:26px;
font-weight:700;
margin-bottom:6px;
}

.rating{
font-size:14px;
color:#666;
margin-bottom:10px;
}

.price{
font-size:24px;
font-weight:700;
color:#f5b84d;
margin-bottom:10px;
}

.weight{
margin-bottom:20px;
}

.weight-options{
display:flex;
gap:10px;
flex-wrap:wrap;
}

.weight-options button{
padding:8px 16px;
border-radius:20px;
border:1px solid #ddd;
background:#fff;
cursor:pointer;
transition:0.3s;
}

.weight-options button.active{
background:#f5b84d;
color:#000;
}

.qty-box{
display:flex;
align-items:center;
gap:10px;
margin-bottom:10px;
}

.qty-box button{
width:35px;
height:35px;
border:none;
background:#f5b84d;
cursor:pointer;
border-radius:6px;
font-size:18px;
}

.total{
margin-bottom:15px;
font-size:16px;
}

.order-btn{
width:100%;
padding:14px;
background:#f5b84d;
border:none;
border-radius:30px;
font-weight:600;
cursor:pointer;
text-align:center;
display:block;
text-decoration:none;
color:#000;
margin-bottom:10px;
transition:0.3s;
}

.order-btn:hover{
opacity:0.9;
}

/* MOBILE */

@media(max-width:768px){

.product-container{
flex-direction:column;
}

.main-img{
height:250px;
}

}

</style>



<section class="product-page">

<div class="product-container">

<!-- IMAGE -->
<div class="product-left">
<div class="main-img">
<img src="https://i0.wp.com/aartimadan.com/wp-content/uploads/2024/10/Meethi-Mathri.jpg?fit=800,449&ssl=1" alt="Meethi Mathri traditional Indian sweet crispy snack">
</div>
</div>

<!-- CONTENT -->
<div class="product-right">

<h2 class="product-title">Ishqana Guteta</h2>
<div class="rating">5 (25 reviews)</div>
<div class="price" id="price">₹99</div>

<p>Authentic crispy snack with desi taste.</p>

<!-- WEIGHT -->
<div class="weight">
<h4>Select Weight</h4>

<div class="weight-options">
<button class="active" data-price="99">250g</button>
<button data-price="179">500g</button>
</div>

</div>

<!-- QUANTITY -->
<h4>Quantity</h4>

<div class="qty-box">
<button onclick="dec()">-</button>
<span id="qty">1</span>
<button onclick="inc()">+</button>
</div>

<!-- TOTAL -->
<div class="total">
<strong>Total: ₹<span id="total">99</span></strong>
</div>

<?php if(isset($_SESSION['user_id'])){ ?>

<a id="cartBtn" class="order-btn">Buy Now</a>

<?php } else { ?>

<a href="auth.php" class="order-btn">Login to Continue</a>

<?php } ?>

</div>

</div>

</section>

<script>

// ===== VARIABLES =====
let basePrice = 99;
let quantity = 1;

let product = "Ishqana Guteta";

// ===== ELEMENTS =====
let qtyEl = document.getElementById("qty");
let totalEl = document.getElementById("total");

// ===== WEIGHT SELECT =====
document.querySelectorAll(".weight-options button").forEach(btn=>{
btn.addEventListener("click",()=>{

document.querySelectorAll(".weight-options button").forEach(b=>b.classList.remove("active"));
btn.classList.add("active");

basePrice = parseInt(btn.dataset.price);
update();

});
});

// ===== QUANTITY =====
function inc(){
quantity++;
update();
}

function dec(){
if(quantity>1){
quantity--;
update();
}
}

// ===== UPDATE FUNCTION =====
function update(){

qtyEl.innerText = quantity;
totalEl.innerText = basePrice * quantity;

// ✅ AUTO IMAGE PICK (MOST IMPORTANT FIX)
let image = document.querySelector(".main-img img").src;

// ✅ FINAL LINK
let cartLink = "add_to_cart.php?product=" + encodeURIComponent(product)
+ "&price=" + basePrice
+ "&qty=" + quantity
+ "&image=" + encodeURIComponent(image);

let cartBtn = document.getElementById("cartBtn");

// 🔥 DEBUG (CHECK IN CONSOLE)
console.log(cartLink);

if(cartBtn){
cartBtn.href = cartLink;
}

}

// ===== INIT =====
update();

</script>
