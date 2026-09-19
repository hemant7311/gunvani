<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Successful | Ishqana</title>

<!-- SEO -->
<meta name="description" content="Your payment was successful at Ishqana. Your order has been placed successfully. Enjoy fresh homemade cookies and cakes delivered to your doorstep.">
<meta name="keywords" content="payment success, order confirmed, Ishqana cookies order, successful payment, bakery order">
<meta name="author" content="Ishqana">
<meta name="robots" content="noindex, follow">

<style>

/* RESET */
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family: 'Poppins', sans-serif;
}

/* BACKGROUND */
body{
min-height:100vh;
background: linear-gradient(135deg, #f0fff4, #d9f7e9);
display:flex;
flex-direction:column;
}

/* CENTER AREA */
.success-wrapper{
flex:1;
display:flex;
justify-content:center;
align-items:center;
padding:80px 20px;
}

/* CARD */
.success-card{
background: rgba(255,255,255,0.75);
backdrop-filter: blur(12px);
border-radius:20px;
padding:60px 40px;
max-width:500px;
width:100%;
text-align:center;
box-shadow: 0 20px 60px rgba(0,0,0,0.1);
animation: fadeIn 0.6s ease;
}

/* ICON */
.success-icon{
font-size:70px;
color:#52c41a;
margin-bottom:20px;
animation: pop 0.5s ease;
}

/* TITLE */
.success-card h1{
font-size:32px;
font-weight:600;
margin-bottom:15px;
color:#222;
}

/* TEXT */
.success-card p{
color:#666;
font-size:16px;
line-height:1.7;
margin-bottom:30px;
}

/* BUTTON GROUP */
.btn-group{
display:flex;
gap:15px;
justify-content:center;
flex-wrap:wrap;
}

/* PRIMARY BTN */
.btn-primary{
padding:14px 30px;
background: linear-gradient(135deg, #52c41a, #389e0d);
color:#fff;
border:none;
border-radius:8px;
font-size:16px;
cursor:pointer;
text-decoration:none;
transition:0.3s;
}

.btn-primary:hover{
transform: translateY(-3px);
box-shadow:0 10px 25px rgba(82,196,26,0.4);
}

/* SECONDARY BTN */
.btn-secondary{
padding:14px 30px;
background:#fff;
color:#333;
border:1px solid #ddd;
border-radius:8px;
font-size:16px;
text-decoration:none;
transition:0.3s;
}

.btn-secondary:hover{
background:#f5f5f5;
}

/* ANIMATIONS */
@keyframes fadeIn{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

@keyframes pop{
from{transform:scale(0.5);}
to{transform:scale(1);}
}

/* RESPONSIVE */
@media(max-width:500px){
.success-card{
padding:40px 20px;
}
.success-card h1{
font-size:26px;
}
}

</style>

</head>

<body>

<?php include 'navar.php'; ?>

<div class="success-wrapper">

    <div class="success-card">
        
        <div class="success-icon">✅</div>

        <h1>Payment Successful</h1>

        <p>
            Thank you! Your payment has been successfully completed.  
            Your order is confirmed and will be processed shortly.  
            We’re excited to deliver fresh happiness to your doorstep ❤️
        </p>

        <div class="btn-group">
            <a href="my_orders.php" class="btn-primary">View Order</a>
            <a href="index.php" class="btn-secondary">Continue Shopping</a>
        </div>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>