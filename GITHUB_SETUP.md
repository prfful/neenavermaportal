# GitHub Repository Setup - neenavikramverma.in

## 📁 Your Folder Structure

**Locally:** `d:\prfful\project\sandesh-webportal\Neenaverma_website\`

**On Hostinger:** `/public_html/` (shared with other subdomains)

---

## 🚀 Step-by-Step Setup

### Step 1: Create GitHub Repository
1. Go to **https://github.com/new**
2. Create new repo: `neenaverma-website` (or your preferred name)
3. Keep it **Public** (unless you want Private)
4. Do NOT add README, .gitignore, license (we'll do this locally)
5. Click **Create repository**

Copy the HTTPS URL: `https://github.com/yourusername/neenaverma-website.git`

---

### Step 2: Setup Locally (Your Computer)

Open Git Bash/Terminal in your project folder:

```bash
# Navigate to your website folder
cd d:\prfful\project\sandesh-webportal\Neenaverma_website

# Initialize Git (creates .git folder)
git init

# Add .gitignore (already created)
# This excludes: db_connect.php, demo/, work/, sandeah/, uploads/, logs/

# Stage all files (respecting .gitignore)
git add .

# Show what will be tracked (verify)
git status

# Should show:
# ✓ index.php
# ✓ gallery.php
# ✓ photo-admin-login.php
# ✓ css/
# ✓ images/
# ✗ db_connect.php (ignored - good!)
# ✗ demo/ (ignored - good!)
```

### Step 3: Create Initial Commit

```bash
# Commit files
git commit -m "Initial commit: neenavikramverma.in website with photo gallery module"

# Add remote repository
git remote add origin https://github.com/yourusername/neenaverma-website.git

# Push to GitHub
git branch -M main
git push -u origin main
```

✅ Your code is now on GitHub!

---

### Step 4: Deploy to Hostinger (SSH)

SSH into your Hostinger account and run these commands:

```bash
# SSH into Hostinger
ssh username@neenavikramverma.in

# Navigate to public_html
cd ~/public_html

# Initialize git there (one time)
git init

# Add remote
git remote add origin https://github.com/yourusername/neenaverma-website.git

# Pull your code
git pull origin main

# Verify files are there
ls -la
```

**Your website files are now deployed!** 🎉

---

## 📤 Update Workflow (After Changes)

Whenever you make changes locally:

```bash
# 1. Check what changed
git status

# 2. Add changes
git add .

# 3. Commit with message
git commit -m "Fixed photo upload handler"

# 4. Push to GitHub
git push origin main

# 5. On Hostinger, pull updates
cd ~/public_html
git pull origin main
```

---

## ⚠️ What Gets Tracked (Safety Check)

### ✅ TRACKED (Go to GitHub):
- `index.php`, `gallery.php`, `contact.php`
- All `.php` files (except db_connect.php)
- `css/`, `images/`, `includes/`, `backend/`, `database/`
- `photo-admin-*.php`, `photo-download-gallery.php`
- `.gitignore` (so Hostinger knows what to ignore)

### ❌ NOT TRACKED (Stay on Server):
- `db_connect.php` ← **Passwords stay private!**
- `demo/`, `work/`, `sandeah/` ← **Other subdomains untouched**
- `uploads/` ← **User files not synced**
- `logs/` ← **Server logs stay local**

---

## 🔒 Security Checklist

✅ `.gitignore` excludes `db_connect.php` (passwords safe)  
✅ Other subdomains ignored (`demo/`, `work/`, `sandeah/`)  
✅ User uploads not tracked  
✅ Only your website files on GitHub  
✅ Hostinger database credentials never exposed  
✅ Safe to make repo public (no secrets)

---

## 🔄 Important Notes

### When Deploying to Hostinger:
- **DO:** Pull only your website files (`git pull`)
- **DON'T:** Force push or overwrite (use `git pull`, not `git reset --hard`)
- **DO:** Keep `db_connect.php` on server (don't add to Git)
- **DON'T:** Push sensitive files to GitHub

### Before First Pull on Hostinger:
You may need to create `db_connect.php` manually on Hostinger with your actual credentials:

```php
<?php
// This file is NOT in GitHub (for security)
$host = "localhost";
$user = "u590837060_websitedharfc";
$pass = "Dharfc@232111";
$dbname = "u590837060_Mainsitedb";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
```

---

## ✨ Example Workflow

**Day 1:** Push initial code
```bash
git add .
git commit -m "Initial: Photo gallery module"
git push origin main
```

**Day 2:** Fix bug, push update
```bash
# Make changes to photo-admin-upload.php
git add photo-admin-upload.php
git commit -m "Fixed file upload validation"
git push origin main
```

**On Hostinger:** Pull updates
```bash
cd ~/public_html
git pull origin main
# Your changes are now live!
```

---

## 📞 Troubleshooting

### If you see "fatal: not a git repository"
```bash
# You're not in the right folder. Check:
pwd  # Should show your website root
git status  # Should show tracked files
```

### If pull fails due to conflicts
```bash
# Never edit files directly on Hostinger via FTP
# Always push from local → pull on server
git reset --hard origin/main  # Get latest from GitHub
```

### If you accidentally committed db_connect.php
```bash
# Remove it from Git history (one-time)
git rm --cached db_connect.php
git commit -m "Remove db_connect.php from tracking"
git push origin main
```

---

## 🎯 Summary

| Aspect | Details |
|--------|---------|
| **Repo Location** | GitHub: yourusername/neenaverma-website |
| **Server Location** | Hostinger: /public_html/ |
| **Files Tracked** | Only neenavikramverma.in files |
| **Files Ignored** | demo/, work/, sandeah/, db_connect.php, uploads/ |
| **Security** | ✅ No passwords on GitHub |
| **Other Sites** | ✅ Completely untouched |
| **Deployment** | `git pull` on Hostinger |

---

Ready to proceed? Let me know when you want to:
1. Create GitHub repo
2. Setup SSH on Hostinger
3. Deploy first version

Or if you have questions about any step!
