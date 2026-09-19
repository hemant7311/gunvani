

<style>

.promise-section{
padding:100px 20px;
background:var(--bg-light);
}

/* TITLE */

.promise-title{
text-align:center;
margin-bottom:60px;
}

.promise-title h2{
font-size:42px;
color:var(--text-dark);
}

.promise-title span{
color:var(--primary);
}

.promise-title p{
color:var(--text-muted);
margin-top:10px;
}

/* GRID */

.promise-container{
max-width:1100px;
margin:auto;
display:grid;
grid-template-columns:repeat(3,1fr);
gap:30px;
}

/* BOX */

.promise-box{
padding:35px 25px;
text-align:center;
border-radius:20px;
background:var(--white);
border:1px solid var(--border);
transition:.3s;
}

.promise-box:hover{
transform:translateY(-8px);
box-shadow:var(--shadow-md);
}

/* ICON */

.promise-icon{
width:70px;
height:70px;
margin:auto;
margin-bottom:18px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
background:var(--primary-soft);
color:var(--primary);
font-size:26px;
}

/* TEXT */

.promise-box h3{
font-size:20px;
margin-bottom:10px;
color:var(--text-dark);
}

.promise-box p{
font-size:14px;
color:var(--text-muted);
line-height:1.6;
}

/* MOBILE */

@media(max-width:992px){
.promise-container{
grid-template-columns:1fr 1fr;
}
}

@media(max-width:600px){
.promise-container{
grid-template-columns:1fr;
}
}

</style>


<section class="promise-section">

<div class="promise-title">

<h2>Our <span>Promise</span></h2>

<p>What makes Ishqana Bakers truly special</p>

</div>


<div class="promise-container">

<!-- ITEM 1 -->
<div class="promise-box">
<div class="promise-icon">
<i class="fa-solid fa-cake-candles"></i>
</div>
<h3>Freshly Baked</h3>
<p>Every product is prepared fresh daily with proper hygiene and care.</p>
</div>

<!-- ITEM 2 -->
<div class="promise-box">
<div class="promise-icon">
<i class="fa-solid fa-heart"></i>
</div>
<h3>Made with Love</h3>
<p>We craft every item with passion to deliver happiness in every bite.</p>
</div>

<!-- ITEM 3 -->
<div class="promise-box">
<div class="promise-icon">
<i class="fa-solid fa-leaf"></i>
</div>
<h3>Pure Ingredients</h3>
<p>Only premium and high-quality ingredients are used for best taste.</p>
</div>

</div>

</section>