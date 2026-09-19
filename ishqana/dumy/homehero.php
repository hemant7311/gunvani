<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}


/* HERO */

.hero-slider{
position:relative;
width:100%;
height:87vh;
overflow:hidden;
}


/* SLIDE */

.slide{
position:absolute;
width:100%;
height:100%;
background-size:cover;
background-position:center;
display:flex;
align-items:center;
justify-content:center;
text-align:center;
opacity:0;
transition:1s;
transform:scale(1.1);
}

.slide.active{
opacity:1;
transform:scale(1);
}


/* OVERLAY */

.slide::before{
content:"";
position:absolute;
top:0;
left:0;
width:100%;
height:100%;
background:linear-gradient(
to bottom,
rgba(0,0,0,0.3),
rgba(0,0,0,0.6)
);
}


/* CONTENT */

.hero-content{
position:relative;
max-width:900px;
padding:20px;
color:white;
animation:fadeUp 1.3s ease;
display:flex;
flex-direction:column;
align-items:center;
justify-content:center;
}


/* TITLE */

.hero-content h1{
font-size:60px;
font-weight:700;
line-height:1.2;
margin-bottom:20px;
text-align:center;
word-break:break-word;
}


/* TEXT */

.hero-content p{
font-size:18px;
margin-bottom:30px;
opacity:0.9;
max-width:650px;
}


/* BUTTON */

.hero-btn{
background:#f5b84d;
color:black;
padding:14px 40px;
border-radius:40px;
font-weight:600;
text-decoration:none;
transition:.3s;
}

.hero-btn:hover{
background:#ffd57a;
transform:scale(1.05);
}


/* NAV DOTS */

.slider-nav{
position:absolute;
bottom:35px;
left:50%;
transform:translateX(-50%);
display:flex;
gap:12px;
}

.slider-nav button{
width:14px;
height:14px;
border-radius:50%;
border:none;
background:#bbb;
cursor:pointer;
transition:.3s;
}

.slider-nav button.active{
background:#f5b84d;
transform:scale(1.2);
}


/* ANIMATION */

@keyframes fadeUp{

0%{
opacity:0;
transform:translateY(60px);
}

100%{
opacity:1;
transform:translateY(0);
}

}


/* RESPONSIVE */

@media(max-width:900px){

.hero-content h1{
font-size:40px;
}

.hero-content p{
font-size:16px;
}

}

@media(max-width:600px){

.hero-slider{
height:80vh;
}

.hero-content h1{
font-size:32px;
}

.hero-btn{
padding:12px 28px;
}

}

</style>



<section class="hero-slider">


<!-- SLIDE 1 -->

<div class="slide active" style="background-image:url('https://images.unsplash.com/photo-1563729784474-d77dbb933a9e')">

<div class="hero-content">

<h1>Sweet Moments Start Here</h1>

<p>Celebrate birthdays, weddings and every special moment with our cakes.</p>

<a href="#" class="hero-btn">Explore Cakes</a>

</div>

</div>



<!-- SLIDE 2 -->

<div class="slide" style="background-image:url('https://images.unsplash.com/photo-1559628233-100c798642d4')">

<div class="hero-content">

<h1>Fresh Handmade Cakes</h1>

<p>Delicious cakes crafted with premium ingredients and love.</p>

<a href="#" class="hero-btn">View Menu</a>

</div>

</div>



<!-- SLIDE 3 -->

<div class="slide" style="background-image:url('https://images.unsplash.com/photo-1603532648955-039310d9ed75')">

<div class="hero-content">

<h1>Perfect Cakes for Every Occasion</h1>

<p>Order online and enjoy fresh bakery flavors delivered to you.</p>

<a href="#" class="hero-btn">Order Now</a>

</div>

</div>



<!-- DOT NAV -->

<div class="slider-nav">

<button class="active"></button>
<button></button>
<button></button>

</div>

</section>



<script>

const slides = document.querySelectorAll(".slide")
const dots = document.querySelectorAll(".slider-nav button")

let index = 0
let interval


function showSlide(i){

slides.forEach(slide => slide.classList.remove("active"))
dots.forEach(dot => dot.classList.remove("active"))

slides[i].classList.add("active")
dots[i].classList.add("active")

index = i

}


dots.forEach((dot,i)=>{

dot.addEventListener("click",()=>{

showSlide(i)

resetInterval()

})

})


function autoSlide(){

index++

if(index >= slides.length){
index = 0
}

showSlide(index)

}


function resetInterval(){

clearInterval(interval)

interval = setInterval(autoSlide,5000)

}


interval = setInterval(autoSlide,5000)

</script>