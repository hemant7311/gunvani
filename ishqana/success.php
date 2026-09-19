<?php
if(isset($_GET['payment_id'])){
    echo "<h2>Payment Successful ✅</h2>";
    echo "<p>Payment ID: " . $_GET['payment_id'] . "</p>";
}else{
    echo "<h2>Invalid Access ❌</h2>";
}