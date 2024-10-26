<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDirectory = 'uploads/';
    $uploadOk = 1;
    $filenameParts = explode('.', $_FILES['image']['name']);
    $fileExtension = strtolower($filenameParts[1]);

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($fileExtension, $allowedExtensions)) {
        $uploadOk = 0;
        $upload_error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    }

    $originalFileName = urldecode($_FILES['image']['name']);
    
    if (strpos($originalFileName, '../') !== false) {
        $originalFileName = str_replace('../', '', $originalFileName);
    }
    $targetFile = $targetDirectory . urldecode($originalFileName);
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;

            $stmt = $db->prepare("INSERT INTO images (user_id, image_path) VALUES (:user_id, :image_path)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->execute();
        } else {
            $upload_error = "Error uploading image.";
        }
    }
}
?>

<?php
$stmt = $db->prepare("SELECT image_path FROM images WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Image Gallery</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <h1 style="text-align:center;margin-top: 15px;">Upload an Image</h1>
    <br>
    <form class="mx-auto" style="max-width:40%;" method="post" enctype="multipart/form-data">
        <input class="form-control" type="file" name="image" required>
        <input style="color:white;" class="form-control bg-dark" type="submit" value="Upload">
    </form>
    <?php if (isset($upload_error)): ?>
        <p style="color: red;text-align:center;">
            <?php echo $upload_error; ?>
        </p>
    <?php endif; ?>
    <br>
    <h1 style="text-align:center;">Image Gallery</h1>

    <hr width="90%" color="white" style="border: 1px solid #000;margin-top: 25px;"/>
    <br>
    <div class="container-fluid mx-auto">
        <div class="row">

            <?php foreach ($images as $image): ?>
                <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                    <img src="<?php echo $image['image_path']; ?>" class="w-100 shadow-1-strong rounded mb-4" />
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</body>

</html>