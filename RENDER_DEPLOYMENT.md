# Deployment Guide - Render.com

## Overview
This guide will help you deploy the University Voting System to Render.com.

---

## Prerequisites

1. **Render.com Account** (free tier available)
   - Sign up at https://render.com
   - Link your GitHub account

2. **GitHub Account**
   - Push your code to GitHub
   - Render deploys from GitHub

3. **Git Installed** (local machine)

---

## Step 1: Prepare GitHub Repository

### Option A: Push Existing Code to GitHub

```bash
cd /home/danmaina/Downloads/voting/kimati-voting-system

# Initialize git if not already done
git init

# Add all files
git add .

# Commit
git commit -m "University Voting System - Ready for deployment"

# Add remote (replace with your GitHub repo URL)
git remote add origin https://github.com/YOUR_USERNAME/voting-system.git
git branch -M main
git push -u origin main
```

### Option B: Create Repository on GitHub

1. Go to https://github.com/new
2. Create a new repository named `voting-system`
3. Copy the repository URL
4. Push your code (use commands from Option A)

---

## Step 2: Deploy on Render.com

### Create Web Service

1. Log in to Render.com
2. Click **"New +"** → **"Web Service"**
3. Select **"Build and deploy from a Git repository"**
4. Connect your GitHub account if not already done
5. Select the `voting-system` repository
6. Fill in the details:
   - **Name**: `voting-system`
   - **Environment**: `PHP`
   - **Build Command**: `echo "Build complete"`
   - **Start Command**: `php -S 0.0.0.0:$PORT`
   - **Plan**: Free (or paid if preferred)

7. Click **"Create Web Service"**

---

## Step 3: Set Up MySQL Database

### Create Database Service

1. On Render.com, click **"New +"** → **"MySQL"**
2. Fill in the details:
   - **Name**: `voting-db`
   - **Database**: `voting_system`
   - **User**: `root` (or custom)
   - **Password**: Generate strong password
   - **Plan**: Free tier

3. Click **"Create Database"**

---

## Step 4: Connect Database to Web Service

### Add Environment Variables to Web Service

1. Go back to your Web Service
2. Click **"Environment"** tab
3. Add these variables:
   - `DB_HOST`: Copy the **Hostname** from MySQL service
   - `DB_USER`: `root` (or what you set)
   - `DB_PASSWORD`: The password you set
   - `DB_NAME`: `voting_system`
   - `DB_PORT`: `3306` (default)

4. Click **"Save"**

---

## Step 5: Initialize Database

### Option A: Using Render Dashboard

1. Go to your MySQL database service
2. Click **"Connect"** tab
3. Copy the connection string
4. Use MySQL Workbench or command line to connect
5. Run the SQL from `database.sql`

### Option B: Automated (Via Web Service)

Add a pre-deployment script to your web service that runs `database.sql` automatically.

---

## Step 6: Verify Deployment

1. Wait for deployment to complete (check the **"Events"** tab)
2. Once live, your app will be at: `https://voting-system.onrender.com`
3. Test the application:
   - Open the URL
   - Register a new user
   - Login with admin credentials

---

## Environment Variables Summary

| Variable | Value | Example |
|---|---|---|
| `DB_HOST` | From MySQL service | `mysql.render.com` |
| `DB_USER` | Database user | `root` |
| `DB_PASSWORD` | Database password | `strong_password_123` |
| `DB_NAME` | Database name | `voting_system` |
| `DB_PORT` | Database port | `3306` |

---

## Troubleshooting

### Database Connection Error
- Check environment variables are correct
- Verify MySQL service is running (green status on Render)
- Ensure IP whitelist allows Render app

### Cannot Upload Images
- Check `uploads/` directory has write permissions
- For Render, images may not persist (use cloud storage like AWS S3)

### Blank Page or 500 Error
- Check **"Logs"** in Render dashboard
- Verify all files were pushed to GitHub
- Check PHP version compatibility

### Database Not Initializing
- Manually run `database.sql` through MySQL connection
- Or add initialization script to web service

---

## Important Notes

### Ephemeral File System

Render.com has an **ephemeral file system**, which means:
- ❌ Uploaded images may be deleted when app restarts
- ✅ Database data persists (MySQL service)

### Solution: Use Cloud Storage

For production, store images on AWS S3, Google Cloud Storage, or Cloudinary:

```php
// Update admin.php to upload to S3 instead of local filesystem
// Or use a service like Cloudinary for image hosting
```

---

## Free Tier Limitations

| Feature | Free Plan |
|---|---|
| Web Service | 750 hrs/month |
| MySQL | Limited storage |
| Uptime | 30-day inactivity spindown |
| Bandwidth | Included |
| SSL/TLS | Included |

---

## Upgrade Path

When you need more:
1. Upgrade to **Starter** or **Standard** plans
2. Get dedicated database
3. Persistent file storage
4. Better performance

---

## Deployment Commands (Summary)

```bash
# 1. Initialize git
git init

# 2. Add files
git add .

# 3. Commit
git commit -m "Initial commit"

# 4. Push to GitHub
git remote add origin https://github.com/YOUR_USERNAME/voting-system.git
git push -u origin main

# 5. Then deploy via Render.com dashboard
# No additional commands needed - Render handles deployment automatically
```

---

## Post-Deployment Checklist

- [ ] Web service deployed and running
- [ ] MySQL database created and connected
- [ ] Environment variables set
- [ ] Database initialized with `database.sql`
- [ ] Admin user created (run setup_admin.php)
- [ ] Test login with admin@university.edu / admin
- [ ] Test image upload
- [ ] Test voting and results
- [ ] Check Charts.js CDN works (needs internet)

---

## Additional Resources

- Render.com Docs: https://render.com/docs
- PHP Deployment: https://render.com/docs/deploy-php
- MySQL on Render: https://render.com/docs/mysql

---

## Support

For issues:
1. Check Render.com logs
2. Review config.php environment variables
3. Test locally first before pushing
4. Check database connectivity

---

**Your app will be live at**: `https://voting-system.onrender.com` 🎉

(Note: Replace with your actual Render service name)
