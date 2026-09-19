<?php
session_start();
include "admin/config/db.php";

// ================= REGISTER =================
if(isset($_POST['register'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        $error = "Email already exists!";
    }else{
        mysqli_query($conn,"INSERT INTO users(name,email,password,mobile) 
        VALUES('$name','$email','$password','$mobile')");

        $success = "Registered! Please login.";
    }
}

// ================= LOGIN =================
if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password,$user['password'])){

            // 🔥 SESSION SET
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // 🔥 REDIRECT
            header("Location: product.php"); // 👈 apna product page
            exit();

        }else{
            $error = "Wrong Password!";
        }

    }else{
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login / Register</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI';}

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:linear-gradient(135deg,#d6cfcf,#6b1d1d);
}

.container{
width:850px;
height:450px;
display:flex;
border-radius:20px;
overflow:hidden;
box-shadow:0 20px 60px rgba(0,0,0,0.2);
}

.left{
flex:1;
background:#6b0d0d;
color:#fff;
padding:60px 40px;
display:flex;
flex-direction:column;
justify-content:center;
}

.right{
flex:1;
background:#fff;
padding:50px 40px;
display:flex;
flex-direction:column;
justify-content:center;
}

input{
width:100%;
padding:12px;
margin-bottom:12px;
border-radius:8px;
border:1px solid #ccc;
}

button{
width:100%;
padding:12px;
background:#f0a23b;
border:none;
border-radius:8px;
color:#fff;
font-weight:600;
cursor:pointer;
}

.toggle{
margin-top:15px;
text-align:center;
cursor:pointer;
}

.error{color:red;text-align:center;}
.success{color:green;text-align:center;}
</style>
</head>

<body>

<div class="container">

<div class="left">
<h1>Ishqana</h1>
<p>Manage your orders easily.</p>
</div>

<div class="right">

<h2 id="title">Register</h2>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>

<form method="POST">

<div id="nameField">
<input type="text" name="name" placeholder="Full Name">
</div>

<div id="mobileField">
<input type="text" name="mobile" placeholder="Mobile Number">
</div>

<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="register" id="btn">Register</button>

</form>

<div class="toggle" onclick="toggleForm()">Already have account? Login</div>

</div>

</div>

<script>

let isLogin = false;

function toggleForm(){

    const nameField = document.getElementById("nameField");
    const mobileField = document.getElementById("mobileField");
    const title = document.getElementById("title");
    const btn = document.getElementById("btn");
    const toggle = document.querySelector(".toggle");

    if(isLogin){

        nameField.style.display = "block";
        mobileField.style.display = "block";

        title.innerText = "Register";
        btn.name = "register";
        btn.innerText = "Register";

        toggle.innerText = "Already have account? Login";

        isLogin = false;

    }else{

        nameField.style.display = "none";
        mobileField.style.display = "none";

        title.innerText = "Login";
        btn.name = "login";
        btn.innerText = "Login";

        toggle.innerText = "Create Account";

        isLogin = true;
    }
}

</script>

</body>
</html>