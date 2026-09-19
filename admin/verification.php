<?php
require_once 'db.php'; // your PDO database connection file

$found = null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = trim($_POST['member_id'] ?? '');

    if ($member_id === '') {
        $message = "⚠️ Please enter Member ID.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = :mid LIMIT 1");
        $stmt->execute([':mid' => $member_id]);
        $found = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$found) {
            $message = "❌ No member found with ID <strong>" . htmlspecialchars($member_id) . "</strong>.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Member Verification | Gunvani News</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  :root {
    --red: #c62828;
    --black: #111;
    --white: #fff;
    --light: #f7f7f7;
  }
  body {
    background: linear-gradient(180deg, var(--light), #fff);
    font-family: Poppins, sans-serif;
    color: var(--black);
  }
  .topbar {
    background: var(--red);
    color: var(--white);
    padding: 14px 20px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  }
  .topbar h3 {
    margin: 0;
    font-weight: 700;
    letter-spacing: 0.5px;
  }
  .container {
    max-width: 800px;
    margin-top: 40px;
  }
  .card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
  }
  .member-photo {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  }
  .label-pill {
    font-size: 13px;
    background: #fff5f5;
    color: var(--red);
    padding: 6px 10px;
    border-radius: 30px;
    display: inline-block;
    margin-top: 10px;
  }
  .footer-note {
    text-align: center;
    font-size: 13px;
    color: #777;
    margin-top: 40px;
  }
</style>
</head>
<body>

<div class="topbar">
  <h3>🔎 Member Verification — Gunvani News</h3>
</div>

<div class="container">
  <div class="card p-4">
    <h5 class="mb-3 text-danger fw-bold">Verify Your Membership</h5>

    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-2 align-items-end">
      <div class="col-md-9">
        <label class="form-label fw-semibold">Enter Member ID</label>
        <input name="member_id" class="form-control" placeholder="e.g. GUN12345" required>
      </div>
      <div class="col-md-3 text-end">
        <button class="btn btn-danger w-100">Verify</button>
      </div>
    </form>
  </div>

  <?php if ($found): ?>
    <div class="card p-4 mt-4">
      <div class="d-flex flex-column flex-md-row align-items-center gap-4">
        <?php
          $photoPath = $found['photo'] ? 'uploads/' . $found['photo'] : 'https://via.placeholder.com/150?text=No+Photo';
          if (strpos($found['photo'], 'uploads/') === 0) $photoPath = $found['photo'];
        ?>
        <img src="<?= htmlspecialchars($photoPath) ?>" class="member-photo" alt="Member Photo">

        <div class="flex-fill">
          <h4 class="mb-1"><?= htmlspecialchars($found['name']) ?></h4>
          <div class="label-pill"><?= htmlspecialchars($found['member_id']) ?></div>

          <p class="mt-3 mb-1"><strong>Mobile:</strong> <?= htmlspecialchars($found['mobile']) ?></p>
          <p class="mb-1"><strong>Blood Group:</strong> <?= htmlspecialchars($found['blood_group']) ?></p>
          <p class="mb-1"><strong>Designation:</strong> <?= htmlspecialchars($found['designation']) ?></p>
          <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($found['address']) ?></p>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="footer-note">
  © <?= date('Y') ?> Gunvani News — All Rights Reserved
</div>

</body>
</html>
