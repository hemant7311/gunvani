<?php include "../auth/check.php"; ?>
<?php include "../config/db.php"; ?>

<?php

// ADD
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $message = $_POST['message'];

    $image = "";
    if(!empty($_FILES['image']['name'])){
        $image = time().$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/".$image);
    }

    mysqli_query($conn,"INSERT INTO testimonials (name,message,image)
    VALUES('$name','$message','$image')");
}

// DELETE
if(isset($_GET['delete'])){
    mysqli_query($conn,"DELETE FROM testimonials WHERE id=".$_GET['delete']);
}

// EDIT
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $message = $_POST['message'];

    if(!empty($_FILES['image']['name'])){
        $image = time().$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/".$image);
        mysqli_query($conn,"UPDATE testimonials SET image='$image' WHERE id=$id");
    }

    mysqli_query($conn,"UPDATE testimonials SET 
    name='$name',
    message='$message'
    WHERE id=$id");
}

// DATA
$result = mysqli_query($conn,"SELECT * FROM testimonials ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Testimonials</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}
body{display:flex;background:#f6f2ee;}

.content{margin-left:260px;padding:25px;width:100%;}

/* TOP */
.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
flex-wrap:wrap;
gap:10px;
}

.page-title{font-size:24px;font-weight:600;}

.add-btn{
background:#f5b84d;
color:#fff;
padding:10px 15px;
border-radius:8px;
cursor:pointer;
}

/* TABLE */
.table-box{
background:#fff;
border-radius:12px;
padding:20px;
box-shadow:0 10px 30px rgba(0,0,0,0.05);
overflow-x:auto; /* 🔥 IMPORTANT */
}

table{
width:100%;
min-width:700px; /* 🔥 force table layout */
border-collapse:collapse;
}

th{
background:#2b0f0f;
color:#fff;
padding:14px;
text-align:left;
}

td{
padding:14px;
border-bottom:1px solid #eee;
}

tr:hover{
background:#fff8ef;
}

/* IMAGE */
.table-img{
width:60px;
height:60px;
border-radius:50%;
object-fit:cover;
}

/* BUTTON */
.btn{
padding:6px 12px;
border-radius:20px;
color:#fff;
text-decoration:none;
font-size:13px;
margin-right:5px;
cursor:pointer;
border:none;
}

.delete{background:#ff4d4d;}
.edit{background:#007bff;}

/* MODAL */
.modal{
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:rgba(0,0,0,0.5);
display:none;
justify-content:center;
align-items:center;
}

.modal-box{
background:#fff;
padding:20px;
border-radius:10px;
width:300px;
max-width:90%;
}

.modal-box input, textarea{
width:100%;
margin:5px 0;
padding:10px;
}

.modal-box button{
background:#f5b84d;
color:#fff;
border:none;
padding:10px;
width:100%;
}

/* MOBILE */
@media(max-width:768px){

body{
flex-direction:column;
}

.content{
margin-left:0;
padding:15px;
}

/* ❌ REMOVE CARD STYLE (IMPORTANT) */
table, thead, tbody, th, td, tr{
display:table;
width:auto;
}

thead{display:table-header-group;}
tbody{display:table-row-group;}
tr{display:table-row;}
td, th{display:table-cell;}

}

</style>
</head>

<body>

<?php include "../partials/sidebar.php"; ?>

<div class="content">

<?php include "../partials/navbar.php"; ?>

<div class="top-bar">
    <div class="page-title">⭐ Testimonials</div>
    <div class="add-btn" onclick="openAdd()">+ Add</div>
</div>

<div class="table-box">

<table>

<tr>
<th>Image</th>
<th>Name</th>
<th>Message</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

<td>
<img src="../uploads/<?php echo $row['image']; ?>" class="table-img">
</td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['message']; ?></td>

<td>
<button class="btn edit"
onclick="openEdit(<?php echo $row['id']; ?>,'<?php echo $row['name']; ?>','<?php echo $row['message']; ?>')">Edit</button>

<a class="btn delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<!-- ADD MODAL -->
<div class="modal" id="addModal">
<div class="modal-box">
<form method="POST" enctype="multipart/form-data">
<input name="name" placeholder="Name" required>
<textarea name="message" placeholder="Message" required></textarea>
<input type="file" name="image" required>
<button name="add">Add</button>
</form>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal" id="editModal">
<div class="modal-box">
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" id="edit_id">
<input name="name" id="edit_name">
<textarea name="message" id="edit_message"></textarea>
<input type="file" name="image">
<button name="edit">Update</button>
</form>
</div>
</div>

<script>

function openAdd(){
document.getElementById("addModal").style.display="flex";
}

function openEdit(id,name,message){
document.getElementById("editModal").style.display="flex";
document.getElementById("edit_id").value=id;
document.getElementById("edit_name").value=name;
document.getElementById("edit_message").value=message;
}

window.onclick = function(e){
if(e.target.classList.contains('modal')){
e.target.style.display="none";
}
}

</script>

</body>
</html>