
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
    .slider-container {
    transform: translateX(-30px);
      position: relative;
      padding: 0 0;
    }
    .slider {
      display: flex;
      gap: 20px;
      transition: transform 0.2s ease;
      will-change: transform;
      padding-left: 10px;
    }
    .card {
      flex: 0 0 33.333%;
      padding: 0;
      box-sizing: border-box;
      position: relative;
    }
    .card-inner {
      background: #fff;
      padding: 20px;
      border-radius: 14px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      text-align: left;
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    .ratings {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
    }
    .ratings img {
      height: 30px;
    }
    .ratings span {
      font-size: 20px;
      font-weight: 600;
    }
    /* Video card style */
    .video-card {
      padding: 0 !important;
    }
    .video-card video {
      width: 100%;
      height: 100%;
      border-radius: 14px;
      object-fit: cover;
      display: block;
    }
    .play-btn {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.8);
      border-radius: 50%;
      font-size: 32px;
      padding: 14px 16px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
      cursor: pointer;
      transition: background 0.2s;
      z-index: 10;
      user-select: none;
    }
    .play-btn:hover {
      background: rgba(255, 255, 255, 1);
    }
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
      margin-bottom: 3px;
      font-size: 16px;
    }
    .testimonial p {
      font-size: 13px;
      color: #666;
    }
    .stars {
      color: #FFA500;
      margin-top: 10px;
    }
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
      transition: background 0.2s;
    }
    .nav-btns button:hover {
      background: #333;
      color: #fff;
    }

    /* Responsive - Mobile */
    @media (max-width: 600px) {
       .slider-wrapper {
      padding:40px 20px;
      text-align: center;
    }

      .slider {
        gap: 15px;
        padding-left: 15px;
      }
      .card {
        flex: 0 0 60%;
      }
      .slider-container {
           transform: translateX(-10px);
        padding-left: 0;
        padding-right: 0;
      }
    }
  </style>


  <div class="slider-wrapper mt-5">
    <h2> Sweet Moments, <span style="color:#f5b84d;">Real Feedback</span></h2>

    <div class="slider-container">
      <div class="slider" id="slider">
       
        <!-- Card 3 -->
        <div class="card">
          <div class="card-inner">
            <div class="testimonial">
              <img src="https://p3.hippopx.com/preview/275/995/man-model-smile-beard-suit-style-km-nazrul-islam-smart-boy-bd-handsome-boy-bd-most-handsome-boy-bd-thumbnail.jpg" alt="User" />
              <div>
                <h4>Pravin Kumar</h4>
               
              </div>
            </div>
          <p>Taste bilkul ghar jaisa hai. Crunch perfect hai.</p>
            <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          </div>
        </div>


        




           <!-- Card 3 -->
        <div class="card">
          <div class="card-inner">
            <div class="testimonial">
              <img src="https://photodpshare.com/wp-content/uploads/2025/10/handsome-boy-dp-hd.jpg" alt="User" />
              <div>
                <h4>Anshul Kumar</h4>
              </div>
            </div>
          <p>Packaging aur freshness dono top level hai.</p>
            <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="card">
          <div class="card-inner">
            <div class="testimonial">
              <img src="https://t3.ftcdn.net/jpg/02/81/81/86/360_F_281818663_XXRCNuGktKeZsnknqWkKI0rR4JPWui3H.jpg" alt="User" />
              <div>
                <h4>Anita Verma</h4>
                <p>Software Tester</p>
              </div>
            </div>
            <p>Delivery fast thi aur product fresh mila.</p>
            <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
          </div>
        </div>
      </div>
    </div>

    <div class="nav-btns">
      <button onclick="moveLeft()">&#10094;</button>
      <button onclick="moveRight()">&#10095;</button>
    </div>
  </div>

  <script>
    const slider = document.getElementById('slider');
    const video = document.getElementById('testimonial-video');
    const playBtn = document.getElementById('play-btn');
    let cards = slider.children;
    let cardWidth = 0;

    // Update card width + gap (used for sliding distance)
    function updateCardWidth() {
      const style = window.getComputedStyle(slider);
      const gap = parseFloat(style.gap) || 20;
      cardWidth = cards[0].offsetWidth + gap;
    }

    // Slide right - move first card to end after slide
    function moveRight() {
      slider.style.transition = 'transform 0.2s ease';
      slider.style.transform = `translateX(-${cardWidth}px)`;
      setTimeout(() => {
        slider.appendChild(slider.firstElementChild);
        slider.style.transition = 'none';
        slider.style.transform = 'translateX(0)';
      }, 510);
    }

    // Slide left - move last card to start before sliding back
    function moveLeft() {
      slider.insertBefore(slider.lastElementChild, slider.firstElementChild);
      slider.style.transition = 'none';
      slider.style.transform = `translateX(-${cardWidth}px)`;
      setTimeout(() => {
        slider.style.transition = 'transform 0.2s ease';
        slider.style.transform = 'translateX(0)';
      }, 20);
    }

    // Auto slide interval
    let autoSlide = setInterval(moveRight, 2000);

    // Pause auto slide on hover
    slider.addEventListener('mouseenter', () => clearInterval(autoSlide));
    slider.addEventListener('mouseleave', () => {
      autoSlide = setInterval(moveRight, 3000);
    });

    // Play/pause video on play button click
    playBtn.addEventListener('click', () => {
      if (video.paused) {
        video.play();
        playBtn.style.display = 'none';
      } else {
        video.pause();
        playBtn.style.display = 'block';
      }
    });

    // Show play button again when video ends
    video.addEventListener('ended', () => {
      playBtn.style.display = 'block';
    });

    // On window resize, update card width
    window.addEventListener('resize', updateCardWidth);

    // Initialize width on load
    updateCardWidth();
  </script>




