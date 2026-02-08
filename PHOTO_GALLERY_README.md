# Photo Download Gallery Module - Documentation

## Overview
This module provides a complete photo download gallery system where admin users can upload event photos that public users can view and download. Photos automatically delete after 5 days from the event date.

## Features
- ✅ Admin/Team user login system
- ✅ Photo upload with event details (name, date, program type, location)
- ✅ Public gallery view with search and filters
- ✅ Individual photo download
- ✅ Bulk download all photos (ZIP)
- ✅ Photos auto-delete after 5 days
- ✅ Sort by date and program type
- ✅ Admin can move selected photos to main gallery
- ✅ Responsive design for mobile/desktop
- ✅ Activity logging
- ✅ Download statistics

## File Structure
```
/
├── photo-admin-login.php           # Admin login page
├── photo-admin-dashboard.php       # Admin dashboard
├── photo-admin-upload.php          # Upload photos interface
├── photo-admin-manage.php          # Manage all photos
├── photo-admin-move.php            # Move photos to main gallery
├── photo-admin-logout.php          # Logout handler
├── photo-download-gallery.php      # Public gallery view
├── includes/
│   └── photo-gallery-db.php        # Database connection & helpers
├── backend/
│   ├── upload-photos-handler.php   # Handle file uploads
│   ├── move-photos-handler.php     # Move photos to main gallery
│   ├── download-photo.php          # Download single photo
│   └── fetch-gallery-data.php      # Fetch gallery data (API)
├── database/
│   └── photo_gallery_structure.sql # Database structure
├── cron-cleanup.php                # Auto-delete expired photos (cron job)
└── uploads/
    └── download_gallery/           # Uploaded photos storage
```

## Installation Steps

### 1. Database Setup
```sql
-- Import the SQL file
mysql -u username -p database_name < database/photo_gallery_structure.sql
```

Or import via phpMyAdmin:
1. Open phpMyAdmin
2. Select your database
3. Go to Import tab
4. Choose `database/photo_gallery_structure.sql`
5. Click Go

### 2. Configure Database Connection
Edit `includes/photo-gallery-db.php`:
```php
$servername = "localhost";
$username = "your_username";     // Change this
$password = "your_password";     // Change this
$dbname = "your_database";       // Change this
```

### 3. Create Upload Directories
```bash
mkdir -p uploads/download_gallery
chmod 755 uploads/download_gallery
```

### 4. Change Default Admin Password
Default credentials:
- Username: `admin`
- Password: `admin123`

**IMPORTANT:** Change this immediately!

Run this SQL to create new admin:
```sql
-- Generate password hash in PHP
-- php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT);"

INSERT INTO photo_gallery_admins (username, password, full_name, role) 
VALUES ('newadmin', '$2y$10$...hash...', 'Admin Name', 'admin');
```

### 5. Set Up Auto-Delete Cron Job
Add to crontab (runs daily at 2 AM):
```bash
crontab -e

# Add this line:
0 2 * * * php /path/to/your/website/cron-cleanup.php
```

## Usage Guide

### For Admins

#### 1. Login
- Navigate to `photo-admin-login.php`
- Enter username and password
- Click Login

#### 2. Upload Photos
1. Click "Upload Photos" from dashboard
2. Fill in event details:
   - Event name (required)
   - Event date (required)
   - Program type (required)
   - Location (optional)
   - Description (optional)
3. Select photos (drag & drop or click to browse)
4. Click "Upload Photos"

**Photo Requirements:**
- Format: JPG, PNG, WEBP
- Max size: 5MB per photo
- Multiple photos allowed

#### 3. Manage Photos
1. Click "Manage Photos" from dashboard
2. Use filters to search events
3. Select photos to delete
4. Edit event details

#### 4. Move Photos to Main Gallery
1. Click "Move to Main Gallery"
2. Select photos you want to move
3. Click "Move Selected to Main Gallery"
4. Photos will be copied to `gallery.php` and marked as moved

### For Public Users

#### View Gallery
- Navigate to `photo-download-gallery.php`
- Browse events and photos
- Use search and filters

#### Download Photos
- **Single photo:** Hover over photo → Click download button (⬇️)
- **All photos:** Click "Download All Photos (ZIP)" button under event

## Database Tables

### download_gallery_events
Stores event information
- `id` - Primary key
- `event_name` - Event name
- `event_date` - Date of event
- `program_type` - Type of program
- `event_location` - Location
- `description` - Event description
- `delete_date` - Auto-delete date (event_date + 5 days)
- `created_at` - Upload timestamp
- `created_by` - Admin username

### download_gallery_photos
Stores photo metadata
- `id` - Primary key
- `event_id` - Foreign key to events
- `filename` - Unique filename
- `original_filename` - Original upload name
- `file_path` - Server path
- `file_size` - Size in bytes
- `download_count` - Number of downloads
- `is_moved_to_main` - Moved to main gallery flag

### photo_gallery_admins
Admin users
- `id` - Primary key
- `username` - Login username
- `password` - Hashed password
- `role` - admin or team

### photo_download_log
Download tracking
- `id` - Primary key
- `photo_id` - Downloaded photo
- `ip_address` - User IP
- `downloaded_at` - Timestamp

## API Endpoints

### Fetch Gallery Data (AJAX)
```javascript
// GET request
fetch('backend/fetch-gallery-data.php?ajax=1&search=query&program_type=type&sort=date_desc')
  .then(response => response.json())
  .then(data => {
    console.log(data.events);
  });
```

### Download Photo
```html
<a href="backend/download-photo.php?photo_id=123">Download</a>
```

## Customization

### Change Auto-Delete Duration
Edit `backend/upload-photos-handler.php`:
```php
// Change from 5 days to 7 days
$delete_date = date('Y-m-d', strtotime($event_date . ' + 7 days'));
```

Also update SQL in `database/photo_gallery_structure.sql`.

### Add More Program Types
Edit dropdown in `photo-admin-upload.php` and `photo-download-gallery.php`:
```html
<option value="नया प्रकार">नया प्रकार</option>
```

### Integrate with Existing Gallery
The move function uses `tbl_gallery` table. Adjust query in `backend/move-photos-handler.php` to match your table structure.

## Security Notes

1. **Change default admin password immediately**
2. **Use HTTPS in production**
3. **Set proper file permissions:**
   ```bash
   chmod 644 *.php
   chmod 755 uploads
   chmod 600 includes/photo-gallery-db.php
   ```
4. **Keep database credentials secure**
5. **Validate file uploads (already implemented)**
6. **Use prepared statements (already implemented)**

## Troubleshooting

### Photos not uploading
- Check folder permissions: `chmod 755 uploads/download_gallery`
- Check PHP upload limits in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 20M
  ```

### Auto-delete not working
- Check cron job is set up correctly
- Check logs in `logs/cleanup.log`
- Run manually: `php cron-cleanup.php`

### Database connection error
- Verify credentials in `includes/photo-gallery-db.php`
- Check MySQL server is running
- Ensure database and tables exist

### Photos not showing
- Check file paths are correct
- Ensure photos exist in `uploads/download_gallery/`
- Check database records match files

## Support & Updates

For issues or feature requests, contact the development team.

---

**Version:** 1.0  
**Last Updated:** February 2026  
**Author:** Development Team
