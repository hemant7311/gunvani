<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<div class="navbar">

    <!-- LEFT -->
    <div class="nav-left">
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
        <h3 class="page-title">Dashboard</h3>
    </div>

    <!-- RIGHT -->
    <div class="nav-right">

        <div class="user-box">
            <div class="avatar">
                <?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?>
            </div>
            <span class="username"><?php echo $_SESSION['user_name']; ?></span>
        </div>

        <a href="#" onclick="openLogout()" class="logout-btn">Logout</a>

    </div>

</div>

<style>
/* NAVBAR */
.navbar{
    width:100%;
    height:70px;
    background:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 25px;
    border-bottom:1px solid #eee;
    position:sticky;
    top:0;
    z-index:1000;
}

/* LEFT */
.nav-left{
    display:flex;
    align-items:center;
    gap:15px;
}

.menu-btn{
    background:#f4b042;
    border:none;
    padding:8px 12px;
    font-size:18px;
    color:#fff;
    border-radius:6px;
    cursor:pointer;
}

.page-title{
    font-size:20px;
    font-weight:600;
    color:#333;
}

/* RIGHT */
.nav-right{
    display:flex;
    align-items:center;
    gap:20px;
}

/* USER BOX */
.user-box{
    display:flex;
    align-items:center;
    gap:10px;
}

/* AVATAR */
.avatar{
    width:35px;
    height:35px;
    background:#2b0f0f;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:50%;
    font-weight:600;
}

/* USERNAME */
.username{
    font-size:14px;
    font-weight:500;
    color:#333;
}

/* LOGOUT BUTTON */
.logout-btn{
    background:#f4b042;
    padding:8px 14px;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    font-size:14px;
    transition:0.3s;
}

.logout-btn:hover{
    background:#d9942f;
}

/* MOBILE */
@media(max-width:768px){

    .username{
        display:none;
    }

    .page-title{
        font-size:16px;
    }

}
</style>

<script>
function toggleSidebar(){
    document.querySelector(".sidebar").classList.toggle("active");
}
</script>