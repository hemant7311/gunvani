<style>

.cta-section{
padding:60px 30px;
background:linear-gradient(135deg,var(--chocolate-light),var(--chocolate));
color:var(--white);
text-align:center;
}

/* GLOW EFFECT */

.cta-section::before{

width:300px;
height:300px;
background:var(--primary);
filter:blur(120px);
opacity:.15;
top:-80px;
left:-80px;
}

/* CONTENT */

.cta-content{
margin:auto;
position:relative;
z-index:2;
}

/* TITLE */

.cta-content h2{
font-size:42px;
margin-bottom:12px;
}

.cta-content h2 span{
color:var(--primary);
}

/* TEXT */

.cta-content p{
color:var(--primary-soft);
margin-bottom:30px;
font-size:16px;
line-height:1.6;
}

/* BUTTONS */

.cta-buttons{
display:flex;
justify-content:center;
gap:15px;
flex-wrap:wrap;
}

.cta-btn{
background:var(--primary);
color:var(--black);
padding:12px 30px;
border-radius:30px;
text-decoration:none;
font-weight:600;
transition:.3s;
}

.cta-btn:hover{
background:var(--primary-hover);
transform:translateY(-3px);
}

/* SECOND BUTTON */

.cta-btn-outline{
border:1px solid var(--primary);
color:var(--primary);
background:transparent;
}

.cta-btn-outline:hover{
background:var(--primary);
color:var(--black);
}

/* MOBILE */

@media(max-width:768px){

.cta-content h2{
font-size:28px;
}

}

</style>


<section class="cta-section">

<div class="cta-content">

<h2>Ready to Taste <span>Something Special?</span></h2>

<p>
Order your favorite cakes and traditional treats today and 
experience the perfect blend of taste and quality.
</p>

<div class="cta-buttons">

<a href="http://localhost/cake/frontend/product.php" class="cta-btn">Explore Products</a>

<a href="http://localhost/cake/frontend/contact.php" class="cta-btn cta-btn-outline">Contact Us</a>

</div>

</div>

</section>