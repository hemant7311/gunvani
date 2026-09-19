<?php

// DATABASE CONNECTION
$conn = mysqli_connect("localhost", "ishqana", "ishqana@20262026", "ishqana");

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

$success = false;

// FORM SUBMIT
if(isset($_POST['submit'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, phone, message)
            VALUES ('$name', '$email', '$phone', '$message')";

    if(mysqli_query($conn, $sql)){
        $success = true;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Contact Form</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

:root{
--white:#ffffff;
--black:#000000;
--primary:#f5b84d;
--primary-hover:#ffcc66;
--primary-soft:#fff3d6;
--text-dark:#1a0d08;
--text-muted:#666;
--bg-light:#f9f9f9;
--bg-soft:#fff8ef;
--border:#eee;
--shadow-sm:0 5px 15px rgba(0,0,0,0.05);
}

body{
font-family:Arial;
margin:0;
}

.contact-section{
padding:80px 20px;
background:var(--bg-light);
}

.contact-container{
max-width:1100px;
margin:auto;
display:flex;
gap:50px;
}

/* LEFT */
.contact-info{
flex:1;
}

.contact-info h2{
font-size:34px;
margin-bottom:15px;
}

.contact-info p{
color:var(--text-muted);
margin-bottom:25px;
}

.info-item{
display:flex;
align-items:center;
gap:12px;
margin-bottom:18px;
}

.info-item i{
color:var(--primary);
background:var(--bg-soft);
padding:10px;
border-radius:50%;
}

/* FORM */
.contact-form{
flex:1;
background:#fff;
padding:40px;
border-radius:20px;
box-shadow:var(--shadow-sm);
}

.contact-form h3{
margin-bottom:20px;
}

.contact-form input,
.contact-form textarea{
width:100%;
padding:12px;
margin-bottom:15px;
border-radius:8px;
border:1px solid var(--border);
outline:none;
}

.contact-btn{
background:var(--primary);
padding:12px;
border:none;
border-radius:30px;
width:100%;
font-weight:600;
cursor:pointer;
}

.contact-btn:hover{
background:var(--primary-hover);
}

/* SUCCESS MESSAGE (UI SAME + SMALL ADD) */
.success-msg{
background:#e6ffed;
color:#1a7f37;
padding:10px;
border-radius:8px;
margin-bottom:15px;
font-size:14px;
}

@media(max-width:768px){
.contact-container{
flex-direction:column;
}
}

</style>

</head>
<body>

<section class="contact-section">

<div class="contact-container">

<!-- LEFT -->
<div class="contact-info">

<h2>Contact Ishqana Bakers</h2>

<p>
Feel free to contact us for orders, queries or support.
We are always ready to serve you fresh and delicious treats.
</p>

<div class="info-item">
<i class="fa-solid fa-phone"></i>
<span>+91 7818014484</span>
</div>

<div class="info-item">
<i class="fa-solid fa-envelope"></i>
<span>ishqanabaker@gmail.com</span>
</div>

<div class="info-item">
<i class="fa-solid fa-location-dot"></i>
<span>
Mainpuri Road, Prem Prakash Chowk,<br>
Shikohabad, Firozabad,<br>
Uttar Pradesh – 283135
</span>
</div>

</div>

<!-- FORM -->
<div class="contact-form">

<h3>Send Message</h3>



<form method="POST">

<input type="text" name="name" placeholder="Your Name" required value="">

<input type="email" name="email" placeholder="Your Email" required value="">

<input type="text" name="phone" placeholder="Phone Number" required value="">

<textarea name="message" rows="4" placeholder="Your Message"></textarea>

<button type="submit" name="submit" class="contact-btn">Send Message</button>

</form>

</div>

</div>

</section>

</body>
</html>