<div class="sidebar">

    <div class="logo">
        <h2>Ishqana</h2>
    </div>

  <ul>

<li>
    <a href="dashboard.php">
        <span class="icon">🏠</span>
        <span>Dashboard</span>
    </a>
</li>

<li>
    <a href="orders.php">
        <span class="icon">📦</span>
        <span>Orders</span>
    </a>
</li>

<li>
    <a href="contact_messages.php">
        <span class="icon">📩</span>
        <span>Contact Messages</span>
    </a>
</li>

<li>
    <a href="testimonials.php">
        ⭐ 
        Testimonials

    </a>
</li>

<li>
    <a href="#" onclick="openLogout()">
        <span class="icon">🚪</span>
        <span>Logout</span>
    </a>
</li>



</ul>

</div>

<!-- LOGOUT MODAL -->
<div class="logout-modal" id="logoutModal">
    <div class="logout-box">
        <h3>Logout</h3>
        <p>Are you sure you want to logout?</p>

        <div class="btns">
            <a href="../auth/logout.php" class="yes">Yes, Logout</a>
            <button onclick="closeLogout()" class="no">Cancel</button>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#2b0f0f,#4b1d1d);
    color:#fff;
    position:fixed;
    left:0;
    top:0;
    padding:20px;
}

/* LOGO */
.logo{
    font-size:22px;
    font-weight:600;
    margin-bottom:30px;
}

/* MENU */
.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    margin:15px 0;
}

.sidebar ul li a{
    text-decoration:none;
    color:#fff;
    padding:12px 15px;
    display:block;
    border-radius:8px;
    transition:0.3s;
}

/* HOVER */
.sidebar ul li a:hover{
    background:#f4b042;
    color:#000;
}

/* ACTIVE */
.sidebar ul li a.active{
    background:#f4b042;
    color:#000;
}

/* LOGOUT MODAL */
.logout-modal{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:9999;
    backdrop-filter:blur(5px);
}

/* MODAL BOX */
.logout-box{
    background:#fff;
    padding:30px 25px;
    border-radius:16px;
    text-align:center;
    width:320px;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
    animation:pop 0.3s ease;
}

.logout-box::before{
    content:"⚠️";
    font-size:40px;
    display:block;
    margin-bottom:10px;
}

.logout-box h3{
    margin-bottom:8px;
    font-size:20px;
    color:#2b0f0f;
}

.logout-box p{
    font-size:14px;
    color:#777;
}

/* BUTTONS */
.btns{
    margin-top:20px;
    display:flex;
    justify-content:center;
    gap:10px;
}

.btns a, 
.btns button{
    padding:10px 16px;
    border:none;
    border-radius:8px;
    font-size:14px;
    cursor:pointer;
    text-decoration:none;
    transition:0.3s;
}

.yes{
    background:#dc3545;
    color:#fff;
}

.yes:hover{
    background:#c82333;
    transform:scale(1.05);
}

.no{
    background:#eee;
    color:#333;
}

.no:hover{
    background:#ddd;
    transform:scale(1.05);
}

/* ANIMATION */
@keyframes pop{
    from{
        transform:scale(0.7);
        opacity:0;
    }
    to{
        transform:scale(1);
        opacity:1;
    }
}

/* MOBILE */
@media(max-width:768px){
    .sidebar{
        left:-260px;
    }

    .sidebar.active{
        left:0;
    }
}

@media(max-width:400px){
    .logout-box{
        width:90%;
    }
}
</style>

<script>
function openLogout(){
    document.getElementById("logoutModal").style.display="flex";
}

function closeLogout(){
    document.getElementById("logoutModal").style.display="none";
}
</script>