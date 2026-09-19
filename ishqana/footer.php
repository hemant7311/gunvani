

<style>

/* =====================
FOOTER (PREMIUM FINAL)
===================== */

.footer{
background:linear-gradient(135deg,var(--chocolate-dark),var(--chocolate));
padding:80px 20px 30px;
color:var(--white);
position:relative;
overflow:hidden;
}

.footer::before{
content:"";
position:absolute;
width:300px;
height:300px;
background:var(--primary);
filter:blur(140px);
top:-100px;
left:-100px;
opacity:0.1;
}

.footer-container{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:40px;
position:relative;
z-index:2;
}

.footer-logo{
font-size:24px;
font-weight:600;
color:var(--primary);
margin-bottom:12px;
display:flex;
align-items:center;
gap:8px;
}

.footer-text{
font-size:14px;
line-height:1.7;
color:#cfcfcf;
margin-bottom:15px;
}

.footer-title{
font-size:18px;
margin-bottom:16px;
color:var(--primary);
}

.footer-links a{
display:block;
text-decoration:none;
color:#cfcfcf;
margin-bottom:10px;
font-size:14px;
transition:.3s;
}

.footer-links a:hover{
color:var(--primary);
padding-left:6px;
}

.footer-contact{
display:flex;
flex-direction:column;
gap:14px;
}

.footer-contact div{
display:flex;
align-items:flex-start;
gap:10px;
font-size:14px;
color:#cfcfcf;
line-height:1.6;
}

.footer-contact i{
color:var(--primary);
margin-top:3px;
}

.footer-social{
margin-top:10px;
}

.footer-social a{
display:inline-flex;
align-items:center;
justify-content:center;
width:38px;
height:38px;
background:rgba(255,255,255,0.08);
color:var(--primary);
border-radius:50%;
margin-right:10px;
font-size:14px;
transition:.3s;
}

.footer-social a:hover{
background:var(--primary);
color:var(--black);
transform:translateY(-3px);
}

.footer-bottom{
text-align:center;
margin-top:50px;
padding-top:20px;
border-top:1px solid rgba(255,255,255,0.08);
font-size:13px;
color:#bfbfbf;
}

/* =========================
FLOATING BUTTONS
========================= */

/* SCROLL TOP */

.scroll-top{
position:fixed;
bottom:30px;
right:30px;
width:45px;
height:45px;
background:var(--primary);
color:var(--black);
border:none;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:18px;
cursor:pointer;
box-shadow:var(--shadow-md);
opacity:0;
visibility:hidden;
transition:.3s;
z-index:999;
}

.scroll-top.show{
opacity:1;
visibility:visible;
}

.scroll-top:hover{
background:var(--primary-hover);
transform:translateY(-3px);
}

/* WHATSAPP */

.whatsapp-btn{
position:fixed;
bottom:30px;
left:30px;
width:50px;
height:50px;
background:#25D366;
color:#fff;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
text-decoration:none;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
z-index:999;
transition:.3s;
}

.whatsapp-btn:hover{
transform:scale(1.1);
}

/* MOBILE */

@media(max-width:768px){

.footer{
text-align:left;
}

.footer-logo{
justify-content:flex-start;
}

.scroll-top{
right:15px;
bottom:20px;
}

.whatsapp-btn{
left:15px;
bottom:20px;
}

}

</style>



<footer class="footer">

<div class="footer-container">

<!-- ABOUT -->

<div>

<a href="index.php" class="ishqana-logo" style="text-decoration:none;  display:flex; align-items:center; gap:8px; margin-bottom:10px">
  <i class="fa-solid fa-cookie-bite"></i>
  <span>Ishqana</span>
</a>

<p class="footer-text">
Authentic Guteta and traditional snacks crafted with love and premium ingredients.
Experience true desi taste in every bite.
</p>

<div class="footer-social">
<a href="#"><i class="fa-brands fa-facebook-f"></i></a>
<a href="#"><i class="fa-brands fa-instagram"></i></a>
<a href="#"><i class="fa-brands fa-youtube"></i></a>
</div>

</div>


<!-- QUICK LINKS -->

<div>

<h3 class="footer-title">Quick Links</h3>

<div class="footer-links">
<a href="index.php">Home</a>
<a href="product.php">Products</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
</div>

</div>


<!-- CONTACT -->

<div>

<h3 class="footer-title">Contact</h3>

<div class="footer-contact">

<div>
<i class="fa-solid fa-phone"></i>
<span>+91 7818014484</span>
</div>

<div>
<i class="fa-solid fa-envelope"></i>
<span>ishqanabaker@gmail.com</span>
</div>

<div>
<i class="fa-solid fa-location-dot"></i>
<span>
Mainpuri Road, Prem Prakash Chowk<br>
Shikohabad, Firozabad<br>
Uttar Pradesh – 283135
</span>
</div>

</div>

</div>

</div>


<div class="footer-bottom">
© 2026 Ishqana Bakers. All Rights Reserved.
</div>

</footer>


<!-- =========================
FLOATING BUTTON HTML
========================= -->

<button class="scroll-top" id="scrollTopBtn">
<i class="fa-solid fa-arrow-up"></i>
</button>

<a href="https://wa.me/917818014484" class="whatsapp-btn" target="_blank">
<i class="fa-brands fa-whatsapp"></i>
</a>


<!-- =========================
JS
========================= -->

<script>

const scrollBtn = document.getElementById("scrollTopBtn")

window.addEventListener("scroll",()=>{
if(window.scrollY > 300){
scrollBtn.classList.add("show")
}else{
scrollBtn.classList.remove("show")
}
})

scrollBtn.onclick = ()=>{
window.scrollTo({
top:0,
behavior:"smooth"
})
}

</script>