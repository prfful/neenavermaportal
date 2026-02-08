<?php
session_start();
if (!isset($_SESSION['mla_loggedin']) || $_SESSION['mla_loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MLA Dashboard - BJP</title>
    <link rel="icon" type="image/png" href="images/bjp-favicon.png">

    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-header {
            background: #ff6f00; /* BJP saffron */
            color: white;
            padding: 20px;
            text-align: center;
        }
        .dashboard-header h1 {
            font-size: 28px;
            margin: 0;
        }
        .card {
            border-radius: 12px;
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
        }
        .logout-btn {
            background: #c62828;
            color: white;
        }
        .logout-btn:hover {
            background: #b71c1c;
        }
    </style>
</head>
<body>

    <div class="dashboard-header">
        <h1>Welcome, <?php echo $_SESSION['mla_user']; ?> 👋</h1>
        <p>Bhartiya Janta Party - Dhar, Madhya Pradesh</p>
    </div>

    <div class="container mt-4">
        <div class="row g-4">

            <!-- Gallery -->
            <div class="col-md-3">
                <a href="upload_gallery.php" class="text-decoration-none">
                    <div class="card text-center p-4">
                        <h4>📸 Photo Gallery</h4>
                        <p>Manage and view images</p>
                    </div>
                </a>
            </div>

            <!-- Vision & Mission -->
            <!-- <div class="col-md-3">
                <a href="vision.php" class="text-decoration-none">
                    <div class="card text-center p-4">
                        <h4>🎯 Vision & Mission</h4>
                        <p>Update goals and achievements</p>
                    </div>
                </a>
            </div> -->

            <!-- Contact Messages -->
            <!-- <div class="col-md-3">
                <a href="contact_messages.php" class="text-decoration-none">
                    <div class="card text-center p-4">
                        <h4>📬 Contact Messages</h4>
                        <p>View messages from public</p>
                    </div>
                </a>
            </div> -->

            <!-- Logout -->
            <div class="col-md-3">
                <a href="logout.php" class="text-decoration-none">
                    <div class="card text-center p-4 logout-btn">
                        <h4>🚪 Logout</h4>
                        <p>End session securely</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

</body>
</html>
