<?php
// DB CONNECTION
$conn = mysqli_connect("localhost", "ishqana", "ishqana@20262026", "ishqana");

// FETCH TESTIMONIALS
$testimonials = mysqli_query($conn,"SELECT * FROM testimonials ORDER BY id DESC");
?>

<style>

.slider-wrapper {
  padding:60px 100px;
  text-align: center;
  background:#fff3d6;
}

.slider-wrapper h2 {
  font-size: 28px;
  margin-bottom: 30px;
  font-weight: 700;
}

/* ✅ FIXED CONTAINER */
.slider-container {
  position: relative;
  overflow: hidden;
  padding: 0 10px; /* spacing instead of transform */
}

/* ✅ SLIDER */
.slider {
  display: flex;
  gap: 20px;
  transition: transform 0.3s ease;
  will-change: transform;
}

/* ✅ PERFECT 3 CARDS (NO CUT) */
.card {
  flex: 0 0 calc((100% - 40px) / 3);
}

/* CARD DESIGN */
.card-inner {
  background: #fff;
  padding: 20px;
  border-radius: 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  text-align: left;
}

/* TESTIMONIAL */
.testimonial {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.testimonial img {
  width: 55px;
  height: 55px;
  border-radius: 50%;
  object-fit: cover;
}

.testimonial h4 {
  font-size: 16px;
  margin-bottom: 2px;
}

.card-inner p {
  font-size: 14px;
  color: #444;
}

/* STARS */
.stars {
  color: #FFA500;
  margin-top: 10px;
}

/* BUTTONS */
.nav-btns {
  margin-top: 25px;
}

.nav-btns button {
  background: #ddd;
  border: none;
  padding: 8px 16px;
  border-radius: 50%;
  margin: 0 5px;
  cursor: pointer;
  transition: 0.2s;
}

.nav-btns button:hover {
  background: #333;
  color: #fff;
}

/* 📱 MOBILE */
@media (max-width: 600px) {

  .slider-wrapper {
    padding:40px 20px;
  }

  .card {
    flex: 0 0 80%;
  }

  .slider-container {
    padding: 0 5px;
  }

}

</style>

<div class="slider-wrapper mt-5">

  <h2>
    Sweet Moments, 
    <span style="color:#f5b84d;">Real Feedback</span>
  </h2>

  <div class="slider-container">
    <div class="slider" id="slider">

      <?php while($row = mysqli_fetch_assoc($testimonials)){ ?>

      <div class="card">
        <div class="card-inner">

          <div class="testimonial">
            <img src="admin/uploads/<?php echo $row['image']; ?>" 
    
     alt="User">
            <div>
              <h4><?php echo $row['name']; ?></h4>
            </div>
          </div>

          <p><?php echo $row['message']; ?></p>

          <div class="stars">★★★★★</div>

        </div>
      </div>

      <?php } ?>

    </div>
  </div>

  <div class="nav-btns">
    <button onclick="moveLeft()">&#10094;</button>
    <button onclick="moveRight()">&#10095;</button>
  </div>

</div>

<script>

const slider = document.getElementById('slider');
let cards = slider.children;
let cardWidth = 0;

// calculate width
function updateCardWidth() {
  const style = window.getComputedStyle(slider);
  const gap = parseFloat(style.gap) || 20;
  cardWidth = cards[0].offsetWidth + gap;
}

// right slide
function moveRight() {
  slider.style.transition = 'transform 0.2s ease';
  slider.style.transform = `translateX(-${cardWidth}px)`;

  setTimeout(() => {
    slider.appendChild(slider.firstElementChild);
    slider.style.transition = 'none';
    slider.style.transform = 'translateX(0)';
  }, 200);
}

// left slide
function moveLeft() {
  slider.insertBefore(slider.lastElementChild, slider.firstElementChild);
  slider.style.transition = 'none';
  slider.style.transform = `translateX(-${cardWidth}px)`;

  setTimeout(() => {
    slider.style.transition = 'transform 0.2s ease';
    slider.style.transform = 'translateX(0)';
  }, 20);
}

// auto slide
let autoSlide = setInterval(moveRight, 2000);

// pause on hover
slider.addEventListener('mouseenter', () => clearInterval(autoSlide));

slider.addEventListener('mouseleave', () => {
  autoSlide = setInterval(moveRight, 3000);
});

// resize fix
window.addEventListener('resize', updateCardWidth);

// init
updateCardWidth();

</script>


 <!-- onerror="this.src='https://via.placeholder.com/55';" -->