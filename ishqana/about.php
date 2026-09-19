<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- BASIC -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO TITLE -->
    <title>About Us - Ishqana Bakery</title>

    <!-- META DESCRIPTION -->
    <meta name="description" content="Learn about Ishqana Bakery, our story, passion, and commitment to delivering premium homemade cookies and cakes with love and quality ingredients.">

    <!-- KEYWORDS -->
    <meta name="keywords" content="about Ishqana, bakery story, homemade cookies, cakes, bakery India, Ishqana brand">

    <!-- AUTHOR -->
    <meta name="author" content="Ishqana">

    <!-- ROBOTS -->
    <meta name="robots" content="index, follow">

</head>

<body>

    <?php include 'navar.php'; ?>

    <main>
        <?php include 'about/abouthero.php'; ?>
        <?php include 'about/story.php'; ?>
        <?php include 'about/promis.php'; ?>
        <?php include 'about/callaction.php'; ?>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>