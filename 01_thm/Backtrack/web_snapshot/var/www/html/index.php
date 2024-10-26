<?php
session_start();

if (isset($_SESSION['user_id'])) {
    require_once 'includes/db.php';
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $db->prepare("SELECT name FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <h1 style="text-align:center;margin-top: 30px;">Welcome back <?php echo $user['name']; ?> !</h1>
    <?php else: ?>
        <h1 style="text-align:center;margin-top: 30px;">Welcome to my image gallery</h1>
        <p style="text-align:center;margin-top: 15px;">Login and start uploading images!</p>
    <?php endif; ?>
</body>
</html>
