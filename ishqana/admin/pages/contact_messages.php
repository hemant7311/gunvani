<?php include "../auth/check.php"; ?>
<?php include "../config/db.php"; ?>

<?php
if(isset($_GET['delete'])){
    mysqli_query($conn,"DELETE FROM contact_messages WHERE id=".$_GET['delete']);
}

$result = mysqli_query($conn,"SELECT * FROM contact_messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Contact Messages</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}
body{display:flex;background:#f6f2ee;}

.content{
    margin-left:260px;
    padding:30px;
    width:100%;
}

/* TOP */
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.page-title{
    font-size:26px;
    font-weight:600;
    color:#2b0f0f;
}

/* TABLE BOX */
.table-box{
    background:#fff;
    border-radius:16px;
    padding:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
    overflow-x:auto; /* 🔥 IMPORTANT */
}

/* TABLE */
table{
    width:100%;
    min-width:800px; /* 🔥 force table width */
    border-collapse:collapse;
}

th{
    background:linear-gradient(90deg,#2b0f0f,#4b1d1d);
    color:#fff;
    padding:14px;
    text-align:left;
    font-weight:500;
    white-space:nowrap;
}

td{
    padding:14px;
    border-bottom:1px solid #f0f0f0;
    color:#333;
    font-size:14px;
    white-space:nowrap;
}

/* ROW HOVER */
tr:hover{
    background:#fff8ef;
}

/* MESSAGE BOX */
.msg{
    max-width:250px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

/* DATE */
.date{
    font-size:13px;
    color:#888;
}

/* DELETE BUTTON */
.btn{
    padding:6px 14px;
    border-radius:20px;
    color:#fff;
    text-decoration:none;
    font-size:13px;
    transition:0.3s;
}

.delete{
    background:#ff4d4d;
}

.delete:hover{
    background:#cc0000;
}

/* SCROLLBAR (OPTIONAL PRO LOOK) */
.table-box::-webkit-scrollbar{
    height:6px;
}
.table-box::-webkit-scrollbar-thumb{
    background:#ccc;
    border-radius:10px;
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

    /* 🔥 KEEP TABLE STRUCTURE */
    table{
        font-size:13px;
    }

}

</style>
</head>

<body>

<?php include "../partials/sidebar.php"; ?>

<div class="content">

<?php include "../partials/navbar.php"; ?>

<div class="top-bar">
    <div class="page-title">📩 Contact Messages</div>
</div>

<div class="table-box">

<table>

<tr>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Message</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['phone']; ?></td>

<td class="msg" title="<?php echo $row['message']; ?>">
<?php echo $row['message']; ?>
</td>

<td class="date"><?php echo $row['created_at']; ?></td>

<td>
<a class="btn delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this message?')">Delete</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>