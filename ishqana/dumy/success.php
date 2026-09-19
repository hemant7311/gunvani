<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success</title>

<style>

/* ================= SUCCESS PAGE ================= */

body{
margin:0;
font-family:Poppins;
background:var(--bg-light);
}

/* WRAPPER (CENTER ALIGN) */

.success-wrapper{
min-height:80vh;
display:flex;
align-items:center;
justify-content:center;
padding:40px 20px;
}

/* BOX */

.success-box{
background:var(--white);
padding:40px 30px;
border-radius:16px;
text-align:center;
max-width:420px;
width:100%;
border:1px solid var(--border);
box-shadow:var(--shadow-sm);
}

/* ICON */

.success-icon{
width:70px;
height:70px;
background:var(--primary-soft);
color:var(--primary);
display:flex;
align-items:center;
justify-content:center;
border-radius:50%;
font-size:28px;
margin:0 auto 15px;
}

/* TITLE */

.success-box h2{
margin-bottom:8px;
color:var(--text-dark);
font-size:22px;
}

/* TEXT */

.success-box p{
font-size:14px;
color:var(--text-muted);
margin-bottom:20px;
line-height:1.5;
}

/* BUTTON */

.success-btn{
display:inline-block;
padding:10px 26px;
background:var(--primary);
color:var(--black);
border-radius:30px;
text-decoration:none;
font-weight:600;
transition:.3s;
}

.success-btn:hover{
background:var(--primary-hover);
transform:translateY(-2px);
}

</style>
</head>

<body>

<?php include 'navar.php'; ?>

<div class="success-wrapper">

<div class="success-box">

<div class="success-icon">✔</div>

<h2>Order Placed Successfully</h2>

<p>Thank you for your order. We will contact you shortly.</p>

<a href="../index.php" class="success-btn">Back to Home</a>

</div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>