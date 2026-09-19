<?php

session_start();
if(!isset($_SESSION['admin'])){
        header("location:http://localhost/manpotech/backend/admin/login");
}



include "../config/db.php";
include "../config/config.php";


/* DELETE DATA */

if(isset($_GET['delete'])){

$id = $_GET['delete'];

$delete = "DELETE FROM jobapply WHERE id='$id'";
mysqli_query($conn,$delete);

header("Location: ../jobenquire/jobenquire.php");
exit();

}

/* FETCH DATA */

$sql = "SELECT * FROM jobapply ORDER BY id DESC";
$result = mysqli_query($conn,$sql);

?>

<h2>Job Applications</h2>

<div class="table-container" style="margin-bottom:100px">

<table>

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Mobile</th>
<th>Gender</th>
<th>DOB</th>
<th>City</th>
<th>Applied For</th>
<th>Resume</th>
<th>Action</th>
</tr>

<?php
while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['mobile']; ?></td>

<td><?php echo $row['gender']; ?></td>

<td><?php echo $row['dob']; ?></td>

<td><?php echo $row['city']; ?></td>

<td><?php echo $row['applyfor']; ?></td>

<td>

<a href="<?php echo 'https://manpotechindia.com/jobcontact/upload/'.$row['resume']; ?>" target="_blank">
View
</a>

|

<a href="<?php echo 'https://manpotechindia.com/jobcontact/upload/'.$row['resume']; ?>" download>
Download
</a>

</td>

<td>

<a href="?delete=<?php echo $row['id']; ?>" 
class="delete-btn"
onclick="return confirm('Are you sure you want to delete this application?')">

Delete

</a>

</td>

</tr>

<?php
}
?>

</table>

</div>


<style>

.table-container{
max-height:450px;
overflow-y:auto;
background:white;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

table{
width:100%;
border-collapse:collapse;
}

th,td{
padding:12px;
border:1px solid #ddd;
font-size:14px;
text-align:left;
}

th{
background:#111827;
color:white;
position:sticky;
top:0;
}

tr:hover{
background:#f9fafb;
}

h2{
margin-bottom:20px;
}

/* DELETE BUTTON */

.delete-btn{
background:red;
color:white;
padding:6px 12px;
text-decoration:none;
border-radius:4px;
font-size:13px;
}

.delete-btn:hover{
background:#c40000;
}

</style>