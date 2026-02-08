<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title : 'श्रीमती नीना विक्रम वर्मा'; ?> | विधायक – धार विधानसभा</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="images/bjplogo.png">
  <link rel="stylesheet" href="css/custom.css">
  <meta name="description" content="श्रीमती नीना विक्रम वर्मा - विधायक धार विधानसभा, मध्य प्रदेश। जनसेवा, विकास और विश्वास के लिए समर्पित।">
  <meta name="keywords" content="नीना विक्रम वर्मा, धार विधानसभा, भाजपा, MLA Dhar, Neena Vikram Verma">
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

  <!-- Navbar -->
  <header class="fixed w-full bg-white shadow-md z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
      <div class="flex items-center space-x-3">
        <img src="images/bjplogo.png" alt="BJP Logo" class="h-12">
        <div>
          <h1 class="text-xl md:text-2xl font-bold text-orange-600">भारतीय जनता पार्टी</h1>
          <p class="text-xs text-gray-600">धार विधानसभा</p>
        </div>
      </div>
      
      <!-- Desktop Navigation -->
      <nav class="space-x-6 hidden lg:flex items-center">
        <a href="index.php" class="hover:text-orange-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-orange-600 font-semibold' : ''; ?>">होम</a>
        <a href="about.php" class="hover:text-orange-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'text-orange-600 font-semibold' : ''; ?>">परिचय</a>
        
        <!-- Development Dropdown -->
        <div class="relative group">
          <button class="hover:text-orange-600 transition flex items-center">
            विकास
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
            <a href="education.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-600">शिक्षा</a>
            <a href="health.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-600">स्वास्थ्य</a>
            <a href="roads.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-600">सड़कें</a>
            <a href="water.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-600">जल</a>
            <a href="electricity.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-600">बिजली</a>
            <a href="urban-development.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-600 rounded-b-lg">शहरी विकास</a>
          </div>
        </div>
        
        <a href="gallery.php" class="hover:text-orange-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'text-orange-600 font-semibold' : ''; ?>">गैलरी</a>
        <a href="photo-download-gallery.php" class="hover:text-orange-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'photo-download-gallery.php' ? 'text-orange-600 font-semibold' : ''; ?>">📸 डाउनलोड गैलरी</a>
        <a href="contact.php" class="hover:text-orange-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'text-orange-600 font-semibold' : ''; ?>">संपर्क</a>
      </nav>
      
      <a href="contact.php" class="hidden lg:inline-block bg-orange-600 text-white px-5 py-2 rounded-lg shadow hover:bg-orange-700 transition">संपर्क करें</a>
      
      <!-- Mobile Menu Button -->
      <button id="mobile-menu-btn" class="lg:hidden text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu lg:hidden bg-white border-t">
      <nav class="container mx-auto px-6 py-4 space-y-2">
        <a href="index.php" class="block py-2 hover:text-orange-600">होम</a>
        <a href="about.php" class="block py-2 hover:text-orange-600">परिचय</a>
        <div class="border-l-2 border-orange-600 pl-4">
          <p class="font-semibold text-gray-700 py-2">विकास</p>
          <a href="education.php" class="block py-2 hover:text-orange-600">→ शिक्षा</a>
          <a href="health.php" class="block py-2 hover:text-orange-600">→ स्वास्थ्य</a>
          <a href="roads.php" class="block py-2 hover:text-orange-600">→ सड़कें</a>
          <a href="water.php" class="block py-2 hover:text-orange-600">→ जल</a>
          <a href="electricity.php" class="block py-2 hover:text-orange-600">→ बिजली</a>
          <a href="urban-development.php" class="block py-2 hover:text-orange-600">→ शहरी विकास</a>
        </div>
        <a href="gallery.php" class="block py-2 hover:text-orange-600">गैलरी</a>
        <a href="photo-download-gallery.php" class="block py-2 hover:text-orange-600">📸 डाउनलोड गैलरी</a>
        <a href="contact.php" class="block py-2 hover:text-orange-600">संपर्क</a>
      </nav>
    </div>
  </header>

  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
      document.getElementById('mobile-menu').classList.toggle('show');
    });
  </script>
