<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Failed | Ishqana</title>

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
background: linear-gradient(135deg, #fff1f0, #ffe5e0);
display:flex;
flex-direction:column;
}

/* CENTER AREA */
.fail-wrapper{
flex:1;
display:flex;
justify-content:center;
align-items:center;
padding:80px 20px;
}

/* CARD */
.fail-card{
background: rgba(255,255,255,0.7);
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
.fail-icon{
font-size:70px;
color:#ff4d4f;
margin-bottom:20px;
animation: pop 0.5s ease;
}

/* TITLE */
.fail-card h1{
font-size:32px;
font-weight:600;
margin-bottom:15px;
color:#222;
}

/* TEXT */
.fail-card p{
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
background: linear-gradient(135deg, #ff4d4f, #d9363e);
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
box-shadow:0 10px 25px rgba(255,77,79,0.4);
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
.fail-card{
padding:40px 20px;
}
.fail-card h1{
font-size:26px;
}
}

</style>

</head>

<body>

<?php include 'navar.php'; ?>

<div class="fail-wrapper">

    <div class="fail-card">
        
        <div class="fail-icon">❌</div>

        <h1>Payment Failed</h1>

        <p>
            Oops! Your payment could not be processed.  
            This might be due to a network issue or interruption.  
            Please try again or choose another payment method.
        </p>

        <div class="btn-group">
            <a href="product.php" class="btn-primary">Retry Payment</a>
            <a href="index.php" class="btn-secondary">Back to Home</a>
        </div>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>