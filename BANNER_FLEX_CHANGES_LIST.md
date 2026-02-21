# बेनर फलेक्स फोटो सिस्टम - परिवर्तनों की सूची

## नई फाइलें (3)

1. **`banner-flex-photo.php`**
   - मुख्य बेनर फोटो गेलरी पृष्ठ
   - खोज, छंटना, और डाउनलोड कार्यक्षमता
   - लाइटबॉक्स व्यू समर्थन

2. **`backend/upload-banner-photos-handler.php`**
   - बेनर फोटो अपलोड प्रक्रिया
   - फाइल सत्यापन और साइज जांच
   - डेटाबेस इंसर्ट और लॉगिंग

3. **`backend/fetch-banner-data.php`**
   - बेनर फोटो डेटा फेचर (AJAX समर्थन के साथ)
   - खोज और छंटना समर्थन

## संशोधित फाइलें (2)

### **`includes/header.php`**
```diff
+ <a href="banner-flex-photo.php" class="hover:text-orange-600 transition">🖼️ बेनर फलेक्‍ स के लिये फोटो</a>
```
- डेस्कटॉप नेविगेशन (2 स्थानों पर)
- मोबाइल नेविगेशन (मेनू सूची में)

### **`photo-admin-upload.php`**
```diff
+ नया सेक्शन: "🖼️ बेनर एवं फलेक्स फोटो अपलोड"
+ फॉर्म फील्ड्स:
  - banner_event_name (आवश्यक)
  - banner_event_date (आवश्यक)
  - banner_event_location (वैकल्पिक)
  - banner_description (वैकल्पिक)
  - banner_dimensions (चयनिका)
  - banner_photos (फाइल अपलोड)
+ JavaScript फ़ंक्शन्स:
  - previewBannerImages()
  - removeBannerImage()
  - बेनर फॉर्म सबमिशन हैंडलर
  - ड्रैग-ड्रॉप समर्थन
```

## डेटाबेस परिवर्तन

### फाइल: `database/add_banner_photos_schema.sql`

**संशोधन**:
```sql
-- 1. कॉलम जोड़ें
ALTER TABLE download_gallery_photos 
ADD COLUMN is_banner_photo tinyint(1) DEFAULT 0;

-- 2. इंडेक्स जोड़ें
CREATE INDEX idx_is_banner_photo 
ON download_gallery_photos (is_banner_photo);

-- 3. नई तालिका बनाएं
CREATE TABLE banner_photo_uploads (
  id int(11) NOT NULL AUTO_INCREMENT,
  photo_id int(11) NOT NULL UNIQUE,
  admin_id int(11) DEFAULT NULL,
  uploaded_at timestamp DEFAULT CURRENT_TIMESTAMP,
  description text DEFAULT NULL,
  dimensions_desc varchar(100) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (photo_id) REFERENCES download_gallery_photos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## सेटअप दस्तावेज़ (1)

1. **`BANNER_FLEX_SETUP_GUIDE.md`**
   - पूर्ण स्थापन निर्देश
   - उपयोग दिशानिर्देश
   - समस्या निवारण
   - डेटाबेस स्कीमा विवरण

## विशेषताएं सारांश

### जनता के दृष्टिकोण से ✅
- [x] मुख्य नेविगेशन में नया लिंक
- [x] समर्पित बेनर फोटो पृष्ठ
- [x] खोज कार्यक्षमता
- [x] डाउनलोड विकल्प
- [x] प्रतिक्रिया डिजाइन

### एडमिन के दृष्टिकोण से ✅
- [x] एडमिन पैनल में नया अपलोड सेक्शन
- [x] एकाधिक फोटो अपलोड
- [x] कस्टम फील्ड्स (आयाम, विवरण)
- [x] अपलोड प्रगति पट्टी
- [x] ड्रैग-ड्रॉप समर्थन

### तकनीकी विशेषताएं ✅
- [x] डेटाबेस स्कीमा विस्तार
- [x] सुरक्षा सत्यापन (लॉगिन, फाइल टाइप)
- [x] फाइल साइज सीमा (10MB)
- [x] अद्वितीय फाइल नाम (UUID)
- [x] गतिविधि लॉगिंग
- [x] त्रुटि हैंडलिंग

## निर्भरताएं

### मौजूदा सिस्टम की आवश्यकताएं:
- ✅ `db_connect.php` (डेटाबेस कनेक्शन)
- ✅ `includes/photo-gallery-db.php` (डेटाबेस यूटिलिटीज़)
- ✅ `includes/header.php` (नेविगेशन)
- ✅ `backend/download-photo.php` (डाउनलोड फ़ंक्शन)
- ✅ Tailwind CSS (स्टाइलिंग)

## निर्देशियाँ बनाएं

```
uploads/
├── banner_photos/          ← नई
│   ├── 2024-01/
│   ├── 2024-02/
│   └── ...
├── download_gallery/       ← मौजूदा
└── gallery/                ← मौजूदा
```

## अनुमतियां (Linux/Mac)

```bash
chmod -R 755 uploads/banner_photos/
chmod 644 banner-flex-photo.php
chmod 644 backend/upload-banner-photos-handler.php
chmod 644 backend/fetch-banner-data.php
```

## परीक्षण चेकलिस्ट

- [ ] डेटाबेस स्कीमा अपडेट किया गया
- [ ] सभी नई फाइलें जगह में हैं
- [ ] `header.php` संशोधन लागू किए गए
- [ ] `photo-admin-upload.php` संशोधन लागू किए गए
- [ ] निर्देशिकाएं बनाई गई और अनुमतियां सेट की गईं
- [ ] एडमिन पैनल में बेनर सेक्शन दिखाई दे रहा है
- [ ] बेनर पृष्ठ `.php` में `is_banner_photo = 1` के साथ फोटो दिखाई दे रहे हैं
- [ ] डाउनलोड कार्यक्षमता काम कर रही है
- [ ] लॉग फाइल अपलोड गतिविधि दिखा रही है

## बैकअप सुझाव

इंस्टॉल करने से पहले इन फाइलों का बैकअप लें:
1. `includes/header.php`
2. `photo-admin-upload.php`
3. डेटाबेस पूरा बैकअप

---

**निर्माण तारीख**: 21 फरवरी 2026  
**संस्करण**: 1.0
