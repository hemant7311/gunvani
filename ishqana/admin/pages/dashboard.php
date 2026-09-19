<?php include "../auth/check.php"; ?>
<?php include "../config/db.php"; ?>

<?php
$total_orders = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM orders"));
$pending = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM orders WHERE status='Pending'"));
$delivered = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM orders WHERE status='Delivered'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

/* BODY */
body{
    background:#f6f2ee;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    z-index:1000;
    transition:0.3s;
}

/* CONTENT */
.content{
    margin-left:260px;
    padding:20px;
    transition:0.3s;
}

/* OVERLAY */
.overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    display:none;
    z-index:999;
}

.overlay.active{
    display:block;
}

/* WELCOME */
.welcome{
    font-size:26px;
    font-weight:600;
    margin:20px 0;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

/* CARD */
.card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

.card h4{
    color:#777;
    font-size:14px;
}

.card h2{
    margin-top:10px;
    color:#2b0f0f;
}

/* MOBILE */
@media(max-width:768px){

    .sidebar{
      padding-top:100px !important;
        left:-260px;
    }

    .sidebar.active{
        left:0;
    }

    .content{
        margin-left:0;
    }

}

</style>
</head>

<body>

<!-- SIDEBAR -->
<?php include "../partials/sidebar.php"; ?>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

<!-- CONTENT -->
<div class="content">

    <!-- NAVBAR -->
    <?php include "../partials/navbar.php"; ?>

    <!-- WELCOME -->
    <div class="welcome">
        Welcome <?php echo $_SESSION['user_name']; ?> 👋
    </div>

    <!-- CARDS -->
    <div class="grid">

        <div class="card">
            <h4>Total Orders</h4>
            <h2><?php echo $total_orders; ?></h2>
        </div>

        <div class="card">
            <h4>Revenue</h4>
            <h2>₹<?php echo $total_orders * 99; ?></h2>
        </div>

        <div class="card">
            <h4>Pending</h4>
            <h2><?php echo $pending; ?></h2>
        </div>

        <div class="card">
            <h4>Delivered</h4>
            <h2><?php echo $delivered; ?></h2>
        </div>

    </div>

</div>

<!-- JS -->
<script>

const sidebar = document.querySelector(".sidebar");
const overlay = document.getElementById("overlay");

function toggleSidebar(){
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
}

overlay.addEventListener("click", ()=>{
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

</script>

</body>
</html>