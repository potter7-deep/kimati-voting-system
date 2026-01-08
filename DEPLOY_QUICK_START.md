# 🚀 Render.com Deployment Summary

## What I've Prepared

✅ **Configuration Files Created:**
- `render.yaml` - Render.com service configuration
- `Procfile` - Web service startup command
- `runtime.txt` - PHP version specification
- `config.production.php` - Production database config template
- `.gitignore` - Git ignore file for deployment
- `uploads/candidates/.gitkeep` - Directory structure

✅ **Updated Files:**
- `config.php` - Now supports environment variables (local & production)

---

## Quick Start: Deploy to Render.com

### Step 1: Prepare Code for GitHub

```bash
cd /home/danmaina/Downloads/voting/kimati-voting-system

# Initialize git repository
git init

# Configure git (first time only)
git config user.name "Your Name"
git config user.email "your@email.com"

# Add all files
git add .

# Commit
git commit -m "University Voting System - Ready for Render deployment"
```

### Step 2: Create GitHub Repository

1. Go to https://github.com/new
2. Create repo named: `voting-system`
3. Copy the repository URL
4. Push code:

```bash
git remote add origin https://github.com/YOUR_USERNAME/voting-system.git
git branch -M main
git push -u origin main
```

### Step 3: Deploy on Render.com

1. **Sign up** at https://render.com (free account)
2. **Connect GitHub** to Render
3. Click **"New +"** → **"Web Service"**
4. Select your `voting-system` repository
5. Set name: `voting-system`
6. Environment: `PHP`
7. Build Command: `echo "Build complete"`
8. Start Command: `php -S 0.0.0.0:$PORT`
9. Plan: **Free**
10. Click **"Create Web Service"**

### Step 4: Add Database

1. Click **"New +"** → **"MySQL"**
2. Name: `voting-db`
3. Database: `voting_system`
4. User: `root`
5. Create strong password (save it!)
6. Plan: **Free**
7. Click **"Create Database"**

### Step 5: Connect Database to Web Service

1. Go to Web Service Settings
2. Click **"Environment"** tab
3. Add variables:

```
DB_HOST = [Copy from MySQL → Connect tab]
DB_USER = root
DB_PASSWORD = [The password you set]
DB_NAME = voting_system
DB_PORT = 3306
```

4. Click **"Save"**

### Step 6: Initialize Database

1. Go to MySQL service
2. Click **"Connect"** tab
3. Copy connection string
4. Connect with MySQL Workbench or CLI
5. Run `database.sql`

### Step 7: Verify Deployment

✓ Wait for Web Service to turn green  
✓ Click the URL to visit your live app  
✓ Should be: `https://voting-system.onrender.com`

---

## Default Admin Credentials

Once deployed:

**Email**: `admin@university.edu`  
**Password**: `admin`

(Or run `setup_admin.php` remotely to create new admin)

---

## Important: Ephemeral File System

Render.com's **free tier has ephemeral storage**, meaning:

❌ Uploaded images may be deleted when app restarts  
✅ Database data persists forever

### Solutions:

**Option 1:** Don't worry (free tier testing)
- Images reset on app restart
- Good for development

**Option 2:** Use Cloud Storage (Production)
- AWS S3
- Google Cloud Storage
- Cloudinary
- DigitalOcean Spaces

---

## Environment Variables Explained

| Variable | Purpose | Example |
|---|---|---|
| `DB_HOST` | Database server | `mysql.render.com` |
| `DB_USER` | Database username | `root` |
| `DB_PASSWORD` | Database password | `xyz123!@#` |
| `DB_NAME` | Database name | `voting_system` |
| `DB_PORT` | Database port | `3306` |

---

## Troubleshooting

**"Cannot connect to database"**
- Check environment variables are correct
- Verify MySQL service is running (green)
- Check IP whitelist

**"Cannot upload images"**
- Images may not persist on free tier
- Solution: Use cloud storage or upgrade plan

**"Blank page / 500 error"**
- Check Logs tab in Render dashboard
- Verify database initialized
- Check PHP version compatibility

**"Setup admin not working"**
- Run directly via MySQL connection
- Or manually insert using SQL:
```sql
INSERT INTO users VALUES (...);
```

---

## Upgrade to Paid (When Needed)

When your app grows:
- Upgrade Web Service to **Starter** or **Standard**
- Upgrade Database to dedicated instance
- Get persistent file storage
- Better performance & uptime

---

## Files Ready for Deployment

```
voting-system/
├── render.yaml              ✓ Render configuration
├── Procfile                 ✓ Startup command
├── runtime.txt              ✓ PHP version
├── config.php               ✓ Updated for env vars
├── config.production.php    ✓ Production template
├── .gitignore               ✓ Git configuration
├── RENDER_DEPLOYMENT.md     ✓ Detailed guide
└── ... (all PHP files)
```

---

## Success Checklist

- [ ] GitHub account created
- [ ] Code pushed to GitHub
- [ ] Render.com account created
- [ ] Web Service deployed (green status)
- [ ] MySQL database created
- [ ] Environment variables set
- [ ] Database initialized
- [ ] Admin user created
- [ ] App accessible at https://voting-system.onrender.com
- [ ] Can login with admin@university.edu / admin
- [ ] Can vote and view results

---

## Next Steps

1. **Push to GitHub** (commands above)
2. **Deploy on Render.com** (steps above)
3. **Test the app** at your live URL
4. **Share the link** with users!

---

**Your app will be live in ~5 minutes! 🎉**

For detailed steps, see `RENDER_DEPLOYMENT.md`
