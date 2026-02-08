<?php
session_start();
if (!isset($_SESSION['mla_loggedin']) || $_SESSION['mla_loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include_once("db_connect.php");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];

    if (!empty($_FILES['images']['name'][0])) {
        $targetDir = "uploads/gallery/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach ($_FILES['images']['name'] as $key => $value) {
            $fileName = time() . "_" . basename($_FILES['images']['name'][$key]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile)) {
                $stmt = $conn->prepare("INSERT INTO gallery (event_name, event_date, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $event_name, $event_date, $targetFile);
                $stmt->execute();
                $stmt->close();
            }
        }

        $msg = "All images uploaded successfully ✅";
    } else {
        $msg = "Please select at least one file ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Event Photos - Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-warning text-white">
            <h3>📸 Upload Event Photos</h3>
        </div>
        <div class="card-body">
            <?php if ($msg): ?>
                <div class="alert alert-info"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Event Name</label>
                    <input type="text" name="event_name" class="form-control" required>
                </div>
                <div class="mb-3 col-md-2">
                    <label class="form-label">Event Date</label>
                    <input type="date" name="event_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Images</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
