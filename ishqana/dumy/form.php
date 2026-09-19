<?php
$conn = mysqli_connect("localhost", "u702612424_manpotechuser", "Jk@8171826717", "u702612424_manpotech");
if(isset($_GET['id'])){
    $id = $_GET['id'];

$sql = "SELECT * FROM jobs WHERE id='$id'";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);

?>


<!-- JOB APPLY FORM SECTION START -->

<section class="job-register-section">

<div class="job-register-container">

<div class="job-register-box">

<h2>Registration</h2>

<form method="POST" action="query.php" class="job-form" enctype="multipart/form-data">

<label>Full Name *</label>
<input type="text" placeholder="Name" required name="name">

<label>Email Address *</label>
<input type="email" placeholder="Email ID" required name="email">

<label>Mobile No. *</label>
<input type="text" placeholder="Mobile No." required name="mobile">


<div class="job-row">

<div>
<label>Gender *</label>

<div class="radio-group">
<label><input type="radio" value="male" name="gender"> Male</label>
<label><input type="radio" value="female" name="gender"> Female</label>
</div>

</div>


<div>
<label>Date of Birth *</label>

<div class="dob-group">

<input type="date" name="dob">

</div>

</div>

</div>


<label>Address *</label>
<input type="text" placeholder="Address" name="address">


<div class="job-row">

<div>
<label>City *</label>
<input type="text" placeholder="City" name="city">
</div>

<div>
<label>Country *</label>
<input type="text" placeholder="Country" name="country">
</div>

</div>

<label>Applied for *</label>
<input type="text" name="applyfor" value="<?php echo $row['job_title']; ?>" readonly>


<label>Attach Resume *</label>
<input type="file" name="resume">

<p class="file-note">
Note: Only pdf, doc, docx file format supported.
</p>


<div class="terms">
<label>
<input type="checkbox"> I agree the User Agreement and Terms & Conditions
</label>
</div>


<button type="submit" class="submit-btn" name="jobsubmit">Submit</button>

</form>

</div>

</div>

</section>
<?php
}

?>

<style>

/* SECTION */

.job-register-section{

position:relative;

padding:100px 20px;

background-image: url("../assets/images/ones.jfif");

background-size:cover;

background-position:center;

overflow:hidden;

}


/* BLUR OVERLAY */

.job-register-section::before{

content:"";

position:absolute;

top:0;
left:0;

width:100%;
height:100%;

background:rgba(0,0,0,0.45);

backdrop-filter:blur(6px);

-webkit-backdrop-filter:blur(6px);

}


/* CONTAINER */

.job-register-container{

position:relative;

max-width:700px;

margin:auto;

}


/* FORM BOX */

.job-register-box{

background:rgba(255,255,255,0.95);

padding:40px;

border-radius:12px;

box-shadow:0 15px 50px rgba(0,0,0,0.2);

}


/* TITLE */

.job-register-box h2{

font-size:30px;

margin-bottom:25px;

color:#111827;

}


/* FORM */

.job-form{

display:flex;

flex-direction:column;

gap:15px;

}


/* INPUT */

.job-form input,
.job-form select{

padding:12px;

border:1px solid #ddd;

border-radius:6px;

font-size:14px;

width:100%;

outline:none;

transition:0.3s;

}


.job-form input:focus,
.job-form select:focus{

border-color:#2563eb;

box-shadow:0 0 0 2px rgba(37,99,235,0.1);

}


/* GRID ROW */

.job-row{

display:grid;

grid-template-columns:1fr 1fr;

gap:20px;

}


/* DOB */

.dob-group{

display:flex;

gap:10px;

}


/* RADIO */

.radio-group{

display:flex;

gap:15px;

margin-top:5px;

}


/* NOTE */

.file-note{

font-size:12px;

color:#6b7280;

}


/* TERMS */

.terms{

font-size:13px;

color:#444;

}


/* BUTTON */

.submit-btn{

background:#2563eb;

color:#fff;

padding:12px;

border:none;

border-radius:6px;

cursor:pointer;

width:130px;

font-weight:600;

transition:0.3s;

}


.submit-btn:hover{

background:#1e40af;

}


/* RESPONSIVE */

@media(max-width:768px){

.job-row{

grid-template-columns:1fr;

}

.job-register-box{

padding:25px;

}

}

</style>

<!-- JOB APPLY FORM SECTION END -->