<style>

.about-section{
padding:100px 20px;
background:var(--bg-soft);
}

/* TOP HEADING */

.about-title{
text-align:center;
margin-bottom:60px;
}

.about-title h2{
font-size:42px;
color:var(--text-dark);
margin-bottom:10px;
}

.about-title h2 span{
color:var(--primary);
}

.about-title p{
color:var(--text-muted);
font-size:16px;
}

/* CONTAINER */

.about-container{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:1fr 1fr;
align-items:center;
gap:70px;
}

/* IMAGE */

.about-img{
width:100%;
height:370px;
border-radius:25px;
overflow:hidden;
box-shadow:var(--shadow-lg);
}

.about-img img{
width:100%;
height:100%;
object-fit:cover;
display:block;
}

/* CONTENT */

.about-content h3{
font-size:36px;
margin-bottom:12px;
color:var(--text-dark);
}

.about-content h3 span{
color:var(--primary);
}

.about-sub{
font-size:18px;
color:var(--text-muted);
margin-bottom:20px;
}

/* TEXT */

.about-content p{
font-size:15px;
color:var(--text-muted);
line-height:1.8;
margin-bottom:15px;
max-width:520px;
}

/* BUTTON */

.about-btn{
display:inline-block;
margin-top:10px;
background:var(--primary);
color:var(--black);
padding:12px 34px;
border-radius:30px;
text-decoration:none;
font-weight:600;
transition:.3s;
}

.about-btn:hover{
background:var(--primary-hover);
transform:translateY(-3px);
}

/* MOBILE */

@media(max-width:992px){

.about-container{
grid-template-columns:1fr;
gap:40px;
}

.about-img{
height:300px;
}

.about-content{
text-align:center;
}

.about-content p{
margin:auto;
}

.about-title h2{
font-size:28px;
}

.about-content h3{
font-size:26px;
}

}

</style>


<section class="about-section">

<!-- TOP HEADING -->
<div class="about-title">

<h2>About <span>Ishqana Bakers</span></h2>

<p>
Discover our journey, passion and commitment to quality.
</p>

</div>


<div class="about-container">

<!-- IMAGE -->
<div class="about-img">
<img src="./assets/images/about2.webp" alt="About Ishqana bakery story homemade cookies and cakes">
</div>

<!-- CONTENT -->
<div class="about-content">

<h3>Our <span>Story</span></h3>

<div class="about-sub">
Crafting taste, tradition and happiness in every bite.
</div>

<p>
Ishqana Bakers was built with a passion for delivering authentic taste 
and premium quality. Every product is carefully crafted to bring joy 
to your celebrations and everyday moments.
</p>

<p>
We combine traditional recipes with modern hygiene standards to ensure 
freshness, flavor and consistency in every bite.
</p>

<a href="http://localhost/cake/frontend/contact.php" class="about-btn">Contact Us</a>

</div>

</div>

</section>