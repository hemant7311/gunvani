<style>

/* =========================
FEATURE BAR (PREMIUM FIXED)
========================= */

.cake-features{
background:var(--bg-soft);
padding:40px 20px;
}

/* GRID */

.features-container{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
}

/* CARD */

.feature-item{
display:flex;
align-items:center;   /* 🔥 FIX: perfect vertical align */
gap:18px;
background:var(--white);
padding:18px 20px;
border-radius:14px;
box-shadow:var(--shadow-sm);
transition:.3s;
min-height:90px;     /* 🔥 FIX: same height */
}

.feature-item:hover{
transform:translateY(-5px);
box-shadow:var(--shadow-md);
}

/* ICON */

.feature-icon{
width:60px;
height:60px;
border-radius:16px;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
flex-shrink:0;
}

/* USING YOUR ROOT COLORS (PREMIUM LOOK) */

.icon-green{
background:var(--primary-soft);
color:var(--primary);
}

.icon-yellow{
background:var(--primary-soft);
color:var(--primary);
}

.icon-blue{
background:var(--primary-soft);
color:var(--primary);
}

.icon-orange{
background:var(--primary-soft);
color:var(--primary);
}

/* TEXT */

.feature-text{
display:flex;
flex-direction:column;
justify-content:center;
}

.feature-text h3{
font-size:18px;
color:var(--text-dark);
margin-bottom:4px;
font-weight:600;
}

.feature-text p{
font-size:14px;
color:var(--text-muted);
line-height:1.4;
}

/* RESPONSIVE */

@media(max-width:992px){
.features-container{
grid-template-columns:repeat(1,1fr);
}
}

@media(max-width:500px){
.features-container{
grid-template-columns:1fr;
}
}

</style>


<section class="cake-features">

<div class="features-container">

<!-- ITEM 1 -->
<div class="feature-item">
<div class="feature-icon icon-green">
<i class="fa-solid fa-cookie-bite"></i>
</div>
<div class="feature-text">
<h3>Authentic Taste</h3>
<p>Traditional Guteta with desi flavor</p>
</div>
</div>

<!-- ITEM 2 -->
<div class="feature-item">
<div class="feature-icon icon-yellow">
<i class="fa-solid fa-star"></i>
</div>
<div class="feature-text">
<h3>Premium Quality</h3>
<p>Made with best ingredients</p>
</div>
</div>

<!-- ITEM 3 -->
<div class="feature-item">
<div class="feature-icon icon-blue">
<i class="fa-solid fa-leaf"></i>
</div>
<div class="feature-text">
<h3>100% Fresh</h3>
<p>Freshly prepared every batch</p>
</div>
</div>

<!-- ITEM 4 -->
<div class="feature-item">
<div class="feature-icon icon-orange">
<i class="fa-solid fa-truck"></i>
</div>
<div class="feature-text">
<h3>Fast Delivery</h3>
<p>Quick delivery to your doorstep</p>
</div>
</div>

</div>

</section>