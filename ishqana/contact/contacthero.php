<style>

.product-hero{
height:60vh;
background:linear-gradient(rgba(0,0,0,0.45),rgba(0,0,0,0.45)),
url('./assets/images/contact1.webp') center/cover;
display:flex;
align-items:center;
justify-content:center;
text-align:center;
color:var(--white);
padding: 0px 20px;
}

.hero-content h1{
font-size:60px;
margin-bottom:15px;
}

.hero-content h1 span{
color:var(--primary);
}

.hero-content p{
font-size:18px;
margin-bottom:25px;
color:var(--primary-soft);
}

.btn{
background:var(--primary);
padding:12px 30px;
border-radius:30px;
color:var(--black);
text-decoration:none;
font-weight:600;
}

/* MOBILE */

@media(max-width:768px){

.hero-content h1{
font-size:32px;
}

.hero-content p{
font-size:14px;
}

}

</style>


<section class="product-hero">

<div class="hero-content">
<h1>Contact <span>Ishqana Bakers</span></h1>

<p>
Have a question or want to place a custom order? 
We’re here to help! Reach out to Ishqana Bakers for fresh cakes, 
delicious snacks, and quick support — we’d love to hear from you.
</p>

<a href="http://localhost/cake/frontend/about.php" class="btn">Know More</a>

</div>

</section>