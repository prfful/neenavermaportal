# Photo Download Gallery Module - Quick Setup Guide

## ✅ What's Been Created

### Frontend Pages (7 files)
1. **photo-admin-login.php** - Admin login page
2. **photo-admin-dashboard.php** - Admin dashboard with statistics
3. **photo-admin-upload.php** - Upload photos with event details
4. **photo-admin-manage.php** - Manage all uploaded photos
5. **photo-admin-move.php** - Select & move photos to main gallery
6. **photo-download-gallery.php** - Public-facing gallery (search, filter, download)
7. **photo-admin-logout.php** - Logout handler

### Backend Scripts (5 files)
1. **backend/upload-photos-handler.php** - Handles file uploads
2. **backend/move-photos-handler.php** - Moves photos to main gallery
3. **backend/download-photo.php** - Single photo download
4. **backend/download-all-photos.php** - ZIP download all event photos
5. **backend/fetch-gallery-data.php** - API to fetch gallery data

### Database & Configuration (3 files)
1. **database/photo_gallery_structure.sql** - Complete database structure
2. **includes/photo-gallery-db.php** - Database connection & helper functions
3. **cron-cleanup.php** - Auto-delete expired photos (run via cron)

### Documentation (2 files)
1. **PHOTO_GALLERY_README.md** - Complete documentation
2. **SETUP_GUIDE.md** - This quick setup guide

## 🚀 Quick Setup (5 Steps)

### Step 1: Import Database
```bash
# Via command line
mysql -u your_username -p your_database < database/photo_gallery_structure.sql

# Or via phpMyAdmin:
# 1. Open phpMyAdmin
# 2. Select your database
# 3. Import → Choose database/photo_gallery_structure.sql
# 4. Click Go
```

### Step 2: Configure Database Connection
Edit `includes/photo-gallery-db.php`:
```php
$servername = "localhost";
$username = "your_db_username";    // ← Change this
$password = "your_db_password";    // ← Change this
$dbname = "your_db_name";          // ← Change this
```

### Step 3: Create Upload Directories
```bash
mkdir -p uploads/download_gallery
chmod 755 uploads/download_gallery
```

### Step 4: Set Up Cron Job (Optional but Recommended)
```bash
crontab -e

# Add this line (runs daily at 2 AM):
0 2 * * * php /full/path/to/cron-cleanup.php
```

### Step 5: Test the Module
1. **Admin Login**: Navigate to `http://yoursite.com/photo-admin-login.php`
   - Username: `admin`
   - Password: `admin123`
   - **⚠️ CHANGE THIS PASSWORD IMMEDIATELY!**

2. **Upload Test Photos**: Click "Upload Photos" and test upload

3. **View Public Gallery**: Visit `http://yoursite.com/photo-download-gallery.php`

## 📱 Access Points

### Admin Panel
- Login: `/photo-admin-login.php`
- Dashboard: `/photo-admin-dashboard.php`

### Public Gallery
- Gallery: `/photo-download-gallery.php`
- Added to main navigation menu automatically ✅

## 🔐 Security Checklist

- [ ] Change default admin password
- [ ] Update database credentials in `includes/photo-gallery-db.php`
- [ ] Set proper file permissions (644 for PHP files, 755 for directories)
- [ ] Enable HTTPS in production
- [ ] Test file upload limits in php.ini

## ⚙️ PHP Configuration

Ensure these settings in `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
```

## 🎨 Features Overview

### For Admins:
✅ Upload photos with event details  
✅ Automatic file naming & organization  
✅ Manage & delete photos  
✅ Move selected photos to main gallery  
✅ Dashboard with statistics  

### For Public Users:
✅ View all recent event photos  
✅ Search by event name  
✅ Filter by program type  
✅ Sort by date  
✅ Download individual photos  
✅ Download all event photos as ZIP  
✅ Auto-expire notice (5 days)  

## 📊 Database Tables Created

1. `download_gallery_events` - Event information
2. `download_gallery_photos` - Photo metadata
3. `photo_gallery_admins` - Admin users
4. `photo_download_log` - Download statistics
5. `photo_gallery_activity_log` - Activity tracking

## 🔄 Auto-Delete Feature

Photos automatically delete 5 days after the event date. This is handled by:
- `cron-cleanup.php` script (must be set up as cron job)
- Runs daily at 2 AM (configurable)
- Logs activity to `logs/cleanup.log`

## 📸 Integration with Main Gallery

Photos can be moved to the main gallery via:
1. Admin panel → "Move to Main Gallery"
2. Select photos
3. Click "Move Selected"
4. Photos are copied to `uploads/gallery/` and added to main gallery table

## 🆘 Troubleshooting

### Photos not uploading?
```bash
chmod 755 uploads/download_gallery
# Check PHP settings: upload_max_filesize, post_max_size
```

### Database connection error?
- Verify credentials in `includes/photo-gallery-db.php`
- Ensure database exists and tables are imported

### Auto-delete not working?
```bash
# Test manually:
php cron-cleanup.php

# Check logs:
cat logs/cleanup.log
```

### Gallery shows empty?
- Check if database has records: `SELECT * FROM download_gallery_events;`
- Verify file paths in database match actual uploaded files

## 📞 Support

For detailed documentation, see: `PHOTO_GALLERY_README.md`

---

**Module Version:** 1.0  
**Created:** February 2026  
**Frontend Ready:** ✅ Complete  
**Backend Ready:** ✅ Complete  
**Database Ready:** ✅ Structure provided  
**Documentation:** ✅ Complete
