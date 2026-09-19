<style>

/* =========================
BESTSELLER SECTION (PRO)
========================= */

.bestseller-section{
padding:90px 20px;
background:var(--bg-light);
}

/* CONTAINER */

.bestseller-container{
max-width:1200px;
margin:auto;
}

/* HEADER */

.bestseller-header{
text-align:center;
margin:0 auto 60px auto;
}

.bestseller-title{
font-size:42px;
font-weight:700;
color:var(--text-dark);
margin-bottom:10px;
}

.bestseller-title span{
color:var(--primary);
}

.bestseller-subtitle{
color:var(--text-muted);
font-size:16px;
line-height:1.6;
}

/* GRID */

.bestseller-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
gap:30px;
}

/* CARD */

.bestseller-card{
background:var(--white);
border-radius:18px;
overflow:hidden;
box-shadow:var(--shadow-sm);
transition:.4s;
display:flex;
flex-direction:column;
height:100%;
}

.bestseller-card:hover{
transform:translateY(-10px);
box-shadow:var(--shadow-md);
}

/* IMAGE */

.bestseller-img{
width:100%;
height:220px;
object-fit:cover;
transition:.4s;
}

.bestseller-card:hover .bestseller-img{
transform:scale(1.08);
}

/* CONTENT */

.bestseller-content{
padding:20px;
display:flex;
flex-direction:column;
flex-grow:1;
}

/* TITLE */

.bestseller-content h3{
font-size:20px;
color:var(--text-dark);
margin-bottom:8px;
}

/* DESC */

.bestseller-content p{
font-size:14px;
color:var(--text-muted);
line-height:1.6;
flex-grow:1;
}

/* BADGE */

.bestseller-badge{
position:absolute;
top:15px;
left:15px;
background:var(--primary);
color:var(--black);
padding:5px 12px;
font-size:12px;
border-radius:20px;
font-weight:600;
}

/* WRAPPER FIX */

.bestseller-image-wrapper{
position:relative;
}

/* MOBILE */

@media(max-width:768px){

.bestseller-title{
font-size:30px;
}

.bestseller-subtitle{
font-size:14px;
}

}

</style>



<section class="bestseller-section">

<div class="bestseller-container">

<!-- HEADER -->
<div class="bestseller-header">

<h2 class="bestseller-title">
Our <span>Bestseller Guteta</span>
</h2>

<p class="bestseller-subtitle">
Our most loved Guteta flavors crafted with authentic desi taste and premium ingredients.
</p>

</div>


<!-- GRID -->
<div class="bestseller-grid">


<!-- ITEM 1 -->
<div class="bestseller-card">

<div class="bestseller-image-wrapper">
<img src="./aa.png" class="bestseller-img">
</div>

<div class="bestseller-content">
<h3>Classic Guteta</h3>
<p>
Traditional crispy Guteta with authentic desi flavor loved by everyone.
</p>
</div>

</div>


<!-- ITEM 2 -->
<div class="bestseller-card">

<div class="bestseller-image-wrapper">
<img src="./aa.png" class="bestseller-img">
</div>

<div class="bestseller-content">
<h3>Sweet Guteta</h3>
<p>
Light sweet crunchy snack with cardamom flavor and rich taste.
</p>
</div>

</div>


<!-- ITEM 3 -->
<div class="bestseller-card">

<div class="bestseller-image-wrapper">
<img src="./aa.png" class="bestseller-img">
</div>

<div class="bestseller-content">
<h3>Premium Mix Guteta</h3>
<p>
Loaded with coconut & cashew for a rich and crunchy experience.
</p>
</div>

</div>


<!-- ITEM 4 -->
<div class="bestseller-card">

<div class="bestseller-image-wrapper">
<img src="./assets/images/slide1.png" class="bestseller-img">
</div>

<div class="bestseller-content">
<h3>Special Desi Guteta</h3>
<p>
Special blend of spices and crunch crafted for true desi lovers.
</p>
</div>

</div>


</div>

</div>

</section>