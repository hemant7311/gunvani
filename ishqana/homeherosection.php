
<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

/* SLIDER */
.mySlider{
  width:100%;
  overflow:hidden;
  position:relative;
  background:#000;
}

.mySlides{
  display:flex;
  transition:transform 0.6s ease;
}

.mySlide{
  min-width:100%;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* IMAGE FIX */
.mySlide img{
  width:100%;
  height:auto; /* IMPORTANT */
  display:block;
}

/* BUTTON */
.myNav{
  position:absolute;
  top:50%;
  transform:translateY(-50%);
  background:rgba(0,0,0,0.5);
  color:#fff;
  border:none;
  padding:10px 15px;
  font-size:20px;
  cursor:pointer;
  z-index:10;
}

.myPrev{left:10px;}
.myNext{right:10px;}

/* DOTS */
.myDots{
  position:absolute;
  bottom:10px;
  left:50%;
  transform:translateX(-50%);
  display:flex;
  gap:8px;
}

.myDot{
  width:10px;
  height:10px;
  background:#aaa;
  border-radius:50%;
  cursor:pointer;
}

.myDot.active{
  background:#fff;
}
</style>

<div class="mySlider">

  <div class="mySlides" id="mySlides">
    <!-- CLONE LAST -->
  <div class="mySlide">
  <a href="http://localhost/cake/frontend/product.php">
    <img src="./assets/images/slider3.webp" alt="Ishqana cookies">
  </a>
</div>

<!-- ORIGINAL -->
<div class="mySlide">
  <a href="http://localhost/cake/frontend/product.php">
    <img src="./assets/images/slider1.webp" alt="Ishqana cookies">
  </a>
</div>

<div class="mySlide">
  <a href="http://localhost/cake/frontend/product.php">
    <img src="./assets/images/slider2.webp" alt="Ishqana cookies">
  </a>
</div>

<div class="mySlide">
  <a href="http://localhost/cake/frontend/product.php">
    <img src="./assets/images/slider3.webp" alt="Ishqana cookies">
  </a>
</div>

<!-- CLONE FIRST -->
<div class="mySlide">
  <a href="http://localhost/cake/frontend/product.php">
    <img src="./assets/images/slider1.webp" alt="Ishqana cookies">
  </a>
</div>
  </div>

  <button class="myNav myPrev">&#10094;</button>
  <button class="myNav myNext">&#10095;</button>

  <div class="myDots"></div>

</div>

<script>
const slides = document.getElementById("mySlides");
const allSlides = document.querySelectorAll(".mySlide");
const total = allSlides.length;

const nextBtn = document.querySelector(".myNext");
const prevBtn = document.querySelector(".myPrev");
const dotsContainer = document.querySelector(".myDots");

let index = 1;
let interval;

/* INITIAL POSITION */
slides.style.transform = `translateX(-${index * 100}%)`;

/* DOTS */
for(let i=0;i<total-2;i++){
  const dot = document.createElement("div");
  dot.classList.add("myDot");
  if(i===0) dot.classList.add("active");

  dot.addEventListener("click", ()=>{
    index = i + 1;
    updateSlider();
    resetAuto();
  });

  dotsContainer.appendChild(dot);
}

const dots = document.querySelectorAll(".myDot");

/* UPDATE */
function updateSlider(){
  slides.style.transition = "transform 0.6s ease";
  slides.style.transform = `translateX(-${index * 100}%)`;

  dots.forEach(dot => dot.classList.remove("active"));
  dots[(index-1) % dots.length].classList.add("active");
}

/* NEXT */
function nextSlide(){
  index++;
  updateSlider();
}

/* PREV */
function prevSlide(){
  index--;
  updateSlider();
}

/* LOOP FIX */
slides.addEventListener("transitionend", ()=>{
  if(index === total-1){
    slides.style.transition = "none";
    index = 1;
    slides.style.transform = `translateX(-${index * 100}%)`;
  }

  if(index === 0){
    slides.style.transition = "none";
    index = total-2;
    slides.style.transform = `translateX(-${index * 100}%)`;
  }
});

/* AUTO */
function startAuto(){
  interval = setInterval(nextSlide,3000);
}

function resetAuto(){
  clearInterval(interval);
  startAuto();
}

/* EVENTS */
nextBtn.addEventListener("click", ()=>{
  nextSlide();
  resetAuto();
});

prevBtn.addEventListener("click", ()=>{
  prevSlide();
  resetAuto();
});

/* INIT */
startAuto();
</script>

