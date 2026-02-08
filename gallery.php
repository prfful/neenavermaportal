<?php
session_start();
//include_once("db_connect.php");

// ✅ Database connection
$conn = new mysqli("localhost", "u590837060_websitedharfc", "Dharfc@232111", "u590837060_Mainsitedb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Fetch events grouped by event_name + event_date (latest first)
$sql = "SELECT event_name, event_date FROM gallery GROUP BY event_name, event_date ORDER BY event_date DESC, uploaded_at DESC";
$events = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>फोटो गैलरी | श्रीमती नीना विक्रम वर्मा</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="images/bjplogo.png">
  <link rel="stylesheet" href="css/custom.css">

  <style>
    /* Masonry effect */
    .masonry { column-count: 3; column-gap: 1rem; }
    @media (max-width: 768px) { .masonry { column-count: 2; } }
    @media (max-width: 640px) { .masonry { column-count: 1; } }

    .masonry-item { break-inside: avoid; margin-bottom: 1rem; position: relative; }

    /* Hover effect */
    .gallery-img {
      transition: transform 0.3s ease-in-out;
    }
    .gallery-img:hover {
      transform: scale(1.05);
    }

    /* Overlay for Download button */
    .overlay {
      position: absolute;
      bottom: 10px;
      right: 10px;
      background: rgba(255, 255, 255, 0.9);
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: bold;
      display: none;
    }
    .masonry-item:hover .overlay {
      display: block;
    }

    /* Lightbox modal */
    .lightbox {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
    }
    .lightbox img {
      max-width: 90%;
      max-height: 80%;
      border-radius: 10px;
    }
    .lightbox:target {
      display: flex;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

<!-- Navbar -->
<?php 
$page_title = "फोटो गैलरी";
include 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-600 to-green-600 py-16 pt-32 text-center text-white">
  <h1 class="text-4xl md:text-5xl font-extrabold">फोटो गैलरी</h1>
  <p class="mt-3 text-lg md:text-xl">यादगार पल और उपलब्धियां</p>
</section>

<!-- Gallery Section -->
<div class="container mx-auto px-6 py-12">
  <?php if ($events->num_rows > 0): ?>
    <?php while($event = $events->fetch_assoc()): ?>
      <div class="mb-12">
        <!-- Event Heading -->
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
          📌 <?php echo htmlspecialchars($event['event_name']); ?>
        </h2>
        <p class="text-sm text-gray-500 mb-4">
          📅 <?php echo date("d M Y", strtotime($event['event_date'])); ?>
        </p>

        <!-- Event Images (Masonry Grid) -->
        <div class="masonry">
          <?php
            $ename = $conn->real_escape_string($event['event_name']);
            $edate = $conn->real_escape_string($event['event_date']);
            $imgSql = "SELECT * FROM gallery WHERE event_name='$ename' AND event_date='$edate' ORDER BY uploaded_at DESC";
            $images = $conn->query($imgSql);
          ?>
          <?php while($img = $images->fetch_assoc()): ?>
            <div class="masonry-item rounded-xl shadow-md overflow-hidden">
              <a href="#lightbox-<?php echo $img['id']; ?>">
                <img src="<?php echo $img['image_path']; ?>" 
                     alt="<?php echo htmlspecialchars($img['event_name']); ?>" 
                     class="gallery-img w-full object-cover rounded-lg">
              </a>
              <!-- Download Button -->
              <a href="<?php echo $img['image_path']; ?>" download class="overlay text-orange-600">
                ⬇ Download
              </a>
            </div>

            <!-- Lightbox -->
            <div id="lightbox-<?php echo $img['id']; ?>" class="lightbox">
              <a href="#" class="absolute top-6 right-6 text-white text-3xl">&times;</a>
              <img src="<?php echo $img['image_path']; ?>" alt="">
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center text-gray-600">कोई कार्यक्रम नहीं मिला।</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

