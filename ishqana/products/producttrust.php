<style>

/* =========================
ORDER BANNER (PREMIUM)
========================= */

.order-section{
background:var(--bg-light);
}

/* BOX */

.order-banner{
margin:auto;
padding:60px 30px;
background:linear-gradient(135deg,var(--chocolate-light),var(--chocolate));
color:var(--white);
text-align:center;
position:relative;
overflow:hidden;
box-shadow:var(--shadow-lg);
}

/* GLOW EFFECT */

.order-banner::before{
content:"";
position:absolute;
width:300px;
height:300px;
background:var(--primary);
filter:blur(120px);
opacity:.15;
top:-80px;
right:-80px;
}

/* TITLE */

.order-banner h2{
font-size:42px;
font-weight:700;
margin-bottom:14px;
line-height:1.3;
}

.order-banner h2 span{
color:var(--primary);
}

/* TEXT */

.order-banner p{
color:var(--primary-soft);
margin-bottom:32px;
font-size:16px;
max-width:650px;
margin-left:auto;
margin-right:auto;
line-height:1.6;
}

/* BUTTON */

.order-btn{
background:var(--primary);
color:var(--black);
padding:14px 38px;
border-radius:40px;
text-decoration:none;
font-weight:600;
display:inline-block;
transition:.3s;
box-shadow:0 10px 25px rgba(245,184,77,0.3);
}

.order-btn:hover{
background:var(--primary-hover);
transform:translateY(-4px);
box-shadow:0 15px 30px rgba(245,184,77,0.4);
}

/* MOBILE */

@media(max-width:768px){

.order-banner{
padding:40px 20px;
}

.order-banner h2{
font-size:26px;
}

.order-banner p{
font-size:14px;
}

}

</style>


<section class="order-section">

<div class="order-banner">

<h2>
Experience the Taste of <span>Ishqana Guteta</span>
</h2>

<p>
Freshly prepared with premium ingredients and traditional recipes, 
our Guteta delivers the perfect crunch and authentic desi flavor in every bite.
</p>

<a href="#abc" class="order-btn">Order Now</a>

</div>

</section>