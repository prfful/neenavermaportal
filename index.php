<?php 
$page_title = "होम";
include 'includes/header.php'; 
?>
  <!-- Hero Section -->
  <section class="relative h-screen flex items-center justify-center bg-gradient-to-br from-orange-600 via-orange-500 to-green-600 text-white pt-20">
    <div class="container mx-auto px-6 text-center">
      <img src="images/neenaverma.jpg" alt="श्रीमती नीना विक्रम वर्मा" class="mx-auto w-48 h-48 md:w-56 md:h-56 rounded-full border-4 border-white shadow-2xl mb-6 object-cover">

      <div class="space-y-2 mb-6">
        <p class="text-lg md:text-xl font-light tracking-wide">जनसेवा | विकास | विश्वास</p>
        <h2 class="text-4xl md:text-6xl font-extrabold">श्रीमती नीना विक्रम वर्मा</h2>
        <p class="text-xl md:text-3xl font-semibold">विधायक – धार विधानसभा</p>
        <p class="text-base md:text-lg mt-4 max-w-2xl mx-auto">धार विधानसभा की निरंतर प्रगति के लिए समर्पित नेतृत्व</p>
      </div>

      <div class="flex flex-wrap justify-center gap-4 mt-8">
        <a href="about.php" class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition">और जानें</a>
        <a href="contact.php" class="bg-green-700 text-white px-8 py-3 rounded-full font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition">संपर्क करें</a>
      </div>
    </div>
    
    <div class="absolute bottom-6 w-full text-center">
      <a href="#impact" class="animate-bounce inline-block text-white text-3xl">↓</a>
    </div>
  </section>

  <!-- Quick Impact Numbers -->
  <section id="impact" class="py-20 bg-white">
    <div class="container mx-auto px-6">
      <h3 class="text-3xl md:text-4xl font-bold text-center mb-12 text-orange-600">विकास के आंकड़े</h3>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Impact Card 1 -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-8 rounded-2xl shadow-lg hover:shadow-xl transition text-center">
          <div class="text-5xl font-extrabold text-orange-600 mb-2">₹300+</div>
          <p class="text-gray-700 font-semibold">करोड़ से अधिक<br>विकास कार्य</p>
        </div>
        
        <!-- Impact Card 2 -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl shadow-lg hover:shadow-xl transition text-center">
          <div class="text-5xl font-extrabold text-green-700 mb-2">300+</div>
          <p class="text-gray-700 font-semibold">निर्माण<br>परियोजनाएँ</p>
        </div>
        
        <!-- Impact Card 3 -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl shadow-lg hover:shadow-xl transition text-center">
          <div class="text-5xl font-extrabold text-blue-600 mb-2">7,000+</div>
          <p class="text-gray-700 font-semibold">लाभार्थी<br>स्वेच्छानुदान</p>
        </div>
        
        <!-- Impact Card 4 -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-2xl shadow-lg hover:shadow-xl transition text-center">
          <div class="text-5xl font-extrabold text-purple-600 mb-2">5+</div>
          <p class="text-gray-700 font-semibold">प्रमुख क्षेत्रों में<br>ऐतिहासिक कार्य</p>
        </div>
      </div>
    </div>
  </section>

  <!-- About Preview Section -->
  <section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <img src="images/6.jpg" alt="नीना विक्रम वर्मा" class="rounded-2xl shadow-2xl">
        
        <div>
          <h3 class="text-3xl md:text-4xl font-bold mb-6 text-orange-600">जनसेवा की यात्रा</h3>
          <p class="text-gray-700 text-lg leading-relaxed mb-4">
            श्रीमती नीना विक्रम वर्मा वर्ष <strong>2008</strong> से लगातार धार विधानसभा की जनता का विश्वास प्राप्त कर रही हैं।
          </p>
          <p class="text-gray-700 text-lg leading-relaxed mb-4">
            भाजपा के चुनाव चिन्ह से <strong>2008, 2013, 2018 एवं 2023</strong> में निर्वाचित होकर उन्होंने विकास को राजनीति का केंद्र बनाया।
          </p>
          <p class="text-gray-700 text-lg leading-relaxed mb-6">
            मुख्यमंत्री के नेतृत्व एवं श्री विक्रम वर्मा के मार्गदर्शन में धार विधानसभा में शिक्षा, स्वास्थ्य, अधोसंरचना, पेयजल और बिजली के क्षेत्र में अभूतपूर्व कार्य हुए हैं।
          </p>
          <a href="about.php" class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-orange-700 hover:shadow-xl transition">
            पूर्ण परिचय पढ़ें →
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Slideshow Section -->
  <section id="slideshow" class="relative h-[500px] w-full overflow-hidden">
    <div class="absolute inset-0">
      <div class="slideshow h-full w-full">
        <div class="slide" style="background-image: url('images/slide1.jpeg');"></div>
        <div class="slide" style="background-image: url('images/slide2.jpg');"></div>
        <div class="slide" style="background-image: url('images/slide3.jpeg');"></div>
        <div class="slide" style="background-image: url('images/slide4.jpeg');"></div>
        <div class="slide" style="background-image: url('images/slide5.jpeg');"></div>
      </div>
    </div>
    
    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
      <h2 class="text-white text-3xl md:text-5xl font-bold text-center px-4">
        "जनता के लिए काम, भविष्य का निर्माण"
      </h2>
    </div>
  </section>

  <!-- Key Funds & Grants Section -->
  <section class="py-20 bg-white">
    <div class="container mx-auto px-6">
      <h3 class="text-3xl md:text-4xl font-bold text-center mb-12 text-orange-600">निधि एवं अनुदान विवरण</h3>
      
      <div class="grid md:grid-cols-3 gap-8">
        <!-- MLA Fund -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-8 rounded-2xl shadow-xl hover:scale-105 transition">
          <div class="text-4xl mb-4">🏗️</div>
          <h4 class="text-2xl font-bold mb-3">विधायक निधि</h4>
          <p class="text-lg mb-2"><strong>₹4.50 करोड़+</strong></p>
          <p class="text-orange-100">201 निर्माण कार्य पूर्ण</p>
          <p class="text-sm text-orange-100 mt-4">विधायक स्थानीय क्षेत्र विकास निधि से धार में व्यापक निर्माण</p>
        </div>
        
        <!-- MP Fund -->
        <div class="bg-gradient-to-br from-green-600 to-green-700 text-white p-8 rounded-2xl shadow-xl hover:scale-105 transition">
          <div class="text-4xl mb-4">🤝</div>
          <h4 class="text-2xl font-bold mb-3">सांसद निधि</h4>
          <p class="text-lg mb-2"><strong>₹3 करोड़</strong></p>
          <p class="text-green-100">100 विकास कार्य</p>
          <p class="text-sm text-green-100 mt-4">राज्यसभा सांसद श्री विक्रम वर्मा के सहयोग से</p>
        </div>
        
        <!-- Voluntary Grant -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white p-8 rounded-2xl shadow-xl hover:scale-105 transition">
          <div class="text-4xl mb-4">❤️</div>
          <h4 class="text-2xl font-bold mb-3">स्वेच्छानुदान</h4>
          <p class="text-lg mb-2"><strong>₹4.09 करोड़</strong></p>
          <p class="text-blue-100">7,381 लाभार्थी</p>
          <p class="text-sm text-blue-100 mt-4">गरीब, विकलांग एवं विद्यार्थियों को आर्थिक सहायता</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Vision & Mission Section -->
  <section id="vision" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
      
      <!-- Text on Left -->
      <div>
        <h3 class="text-3xl font-bold mb-6 text-orange-600">दृष्टि और लक्ष्य</h3>
        
        <div class="mb-6">
          <h4 class="text-xl font-semibold text-green-700 mb-2">दृष्टि</h4>
          <p class="text-gray-700">
            एक मजबूत, आत्मनिर्भर और समृद्ध निर्वाचन क्षेत्र का निर्माण करना जहां प्रत्येक नागरिक को शिक्षा, स्वास्थ्य सेवा, स्वच्छ जल और विकास के समान अवसर उपलब्ध हों।
          </p>
        </div>
        
        <div>
          <h4 class="text-xl font-semibold text-green-700 mb-2">लक्ष्य</h4>
          <p class="text-gray-700">
            पारदर्शिता के साथ सेवा करना, युवाओं और महिलाओं को सशक्त बनाना, सतत विकास को बढ़ावा देना और लोगों के कल्याण के लिए भारतीय जनता पार्टी के मूल्यों को बनाए रखना।
          </p>
        </div>
      </div>
      
      <!-- Image on Right -->
      <img src="images/vision.png" 
           alt="Vision & Mission" 
           class="rounded-2xl shadow-lg">
      
    </div>
  </section>



  <!-- Initiatives Section -->
  <!-- <section id="initiatives" class="py-20 bg-gray-100">
    <div class="container mx-auto px-6 text-center">
      <h3 class="text-3xl font-bold mb-12 text-orange-600">Key Initiatives</h3>
      <div class="grid md:grid-cols-3 gap-10">
        <div class="bg-white p-8 rounded-2xl shadow hover:shadow-xl transition">
          <h4 class="text-xl font-semibold mb-4 text-green-700">Education</h4>
          <p class="text-gray-600">Smart classrooms, scholarships, and skill development programs for students.</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow hover:shadow-xl transition">
          <h4 class="text-xl font-semibold mb-4 text-green-700">Healthcare</h4>
          <p class="text-gray-600">Free health camps, improved hospitals, and mobile healthcare units in villages.</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow hover:shadow-xl transition">
          <h4 class="text-xl font-semibold mb-4 text-green-700">Infrastructure</h4>
          <p class="text-gray-600">Better roads, clean drinking water, and solar energy initiatives for villages.</p>
        </div>
      </div>
    </div>
  </section> -->

  <!-- Gallery Section -->
<!-- Photo Gallery Section -->
<section id="gallery" class="py-20 bg-white">
  <div class="container mx-auto px-6 text-center">
    <h3 class="text-3xl font-bold text-orange-600 mb-10">Photo Gallery</h3>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <img src="images/g5.jpg" alt="Event 1" class="rounded-lg shadow-lg hover:scale-105 transition">
      <img src="images/g3.jpg" alt="Photo 2" class="rounded-lg shadow-lg hover:scale-105 transition">
      <img src="images/g4.jpg" alt="Photo 3" class="rounded-lg shadow-lg hover:scale-105 transition">
      <img src="images/g5.jpg" alt="Photo 4" class="rounded-lg shadow-lg hover:scale-105 transition">
    </div>

    <!-- More Button -->
    <div class="mt-10">
      <a href="gallery.php" 
         class="bg-orange-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-orange-700 transition">
        More →
      </a>
    </div>
  </div>
</section>

  

  <!-- Contact Section -->
  <!-- <section id="contact" class="py-20 bg-gray-100">
    <div class="container mx-auto px-6 text-center">
      <h3 class="text-3xl font-bold mb-6 text-orange-600">Get in Touch</h3>
      <p class="text-gray-600 mb-8">Reach out for constituency queries, grievances, or suggestions.</p>
      <form class="max-w-lg mx-auto grid gap-4">
        <input type="text" placeholder="Your Name" class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-orange-600">
        <input type="email" placeholder="Your Email" class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-orange-600">
        <textarea placeholder="Your Message" rows="5" class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-orange-600"></textarea>
        <button class="bg-orange-600 text-white px-6 py-3 rounded-lg shadow hover:bg-orange-700 transition">Send Message</button>
      </form>
    </div>
  </section> -->

<?php include 'includes/footer.php'; ?>
