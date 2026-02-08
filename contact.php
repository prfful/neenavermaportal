<?php 
$page_title = "संपर्क करें";
include 'includes/header.php'; 
?>

  <!-- Hero Section -->
  <section class="bg-gradient-to-r from-orange-600 to-green-600 text-white text-center py-20 pt-32">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4">संपर्क करें</h1>
    <p class="text-lg md:text-xl">धार विधानसभा के विकास के लिए आपके सुझाव और समस्याएं हमारे लिए महत्वपूर्ण हैं</p>
  </section>

  <!-- Contact Details -->
  <section class="container mx-auto px-6 py-12 grid md:grid-cols-2 gap-12">
    
    <!-- Left: Details -->
    <div>
      <h2 class="text-3xl font-bold text-orange-600 mb-6">कार्यालय की जानकारी</h2>
      
      <div class="space-y-4 text-gray-700">
        <div class="flex items-start">
          <span class="text-2xl mr-3">👤</span>
          <div>
            <p class="font-semibold">नाम</p>
            <p>श्रीमती नीना विक्रम वर्मा</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <span class="text-2xl mr-3">🏛️</span>
          <div>
            <p class="font-semibold">विधानसभा क्षेत्र</p>
            <p>धार, मध्य प्रदेश</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <span class="text-2xl mr-3">🟠</span>
          <div>
            <p class="font-semibold">दल</p>
            <p>भारतीय जनता पार्टी (भाजपा)</p>
          </div>
        </div>
      </div>

      <div class="mt-8 p-6 bg-orange-50 rounded-xl">
        <h3 class="text-xl font-bold text-orange-600 mb-3">जनसुनवाई</h3>
        <p class="text-gray-700">
          अपनी समस्याओं और सुझावों के लिए आप हमारे कार्यालय में संपर्क कर सकते हैं। 
          हम आपकी सेवा के लिए सदैव तत्पर हैं।
        </p>
      </div>
    </div>

    <!-- Right: Map -->
    <div class="space-y-6">
      <div class="rounded-xl shadow-lg overflow-hidden">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3699.2018952785755!2d75.299!3d22.600!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3962f38b9b90f4ff%3A0x2a6e168f99b0a7f!2sDhar%2C%20Madhya%20Pradesh!5e0!3m2!1sen!2sin!4v1692250000000"
          width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
      
      <div class="bg-green-50 p-6 rounded-xl">
        <h3 class="text-xl font-bold text-green-700 mb-3">महत्वपूर्ण लिंक</h3>
        <div class="space-y-2">
          <a href="gallery.php" class="block text-orange-600 hover:underline">→ फोटो गैलरी</a>
          <a href="education.php" class="block text-orange-600 hover:underline">→ शिक्षा विकास</a>
          <a href="health.php" class="block text-orange-600 hover:underline">→ स्वास्थ्य सुविधाएं</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Social Media & Connect -->
  <section class="py-16 bg-gradient-to-r from-orange-100 to-green-100">
    <div class="container mx-auto px-6 text-center">
      <h3 class="text-3xl font-bold text-orange-600 mb-8">सोशल मीडिया पर जुड़ें</h3>
      <p class="text-lg text-gray-700 mb-8">नवीनतम अपडेट और कार्यक्रमों की जानकारी के लिए हमें फॉलो करें</p>
      
      <div class="flex justify-center gap-6">
        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full transition">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
          </svg>
        </a>
        <a href="#" class="bg-blue-400 hover:bg-blue-500 text-white p-4 rounded-full transition">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
          </svg>
        </a>
        <a href="#" class="bg-pink-600 hover:bg-pink-700 text-white p-4 rounded-full transition">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"/>
          </svg>
        </a>
      </div>
    </div>
  </section>

<?php include 'includes/footer.php'; ?>
