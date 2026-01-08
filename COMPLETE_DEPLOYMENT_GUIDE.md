# 🎯 Complete Render.com Deployment Guide

**Status**: ✅ Ready to Deploy  
**Commit Created**: University Voting System - Production Ready

---

## Step 1️⃣: Create GitHub Repository

### Option A: Create New Repository

1. Go to **https://github.com/new**
2. Repository name: `voting-system`
3. Description: `University Voting System with Charts and Image Uploads`
4. Choose **Public** or **Private**
5. Click **"Create repository"**
6. Copy the repository URL (looks like: `https://github.com/YOUR_USERNAME/voting-system.git`)

### Option B: Use GitHub CLI

```bash
gh repo create voting-system --public --source=. --remote=origin --push
```

---

## Step 2️⃣: Push Code to GitHub

Run these commands in your terminal:

```bash
cd /home/danmaina/Downloads/voting/kimati-voting-system

# Add remote origin (replace YOUR_USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/voting-system.git

# Set main branch
git branch -M main

# Push code to GitHub
git push -u origin main
```

**What this does:**
- Connects your local code to GitHub
- Pushes all files to your GitHub repository
- Sets main as default branch

**Wait for**: Code to appear on GitHub (refresh browser)

---

## Step 3️⃣: Create Render.com Account

1. Go to **https://render.com**
2. Click **"Get Started"**
3. Sign up with:
   - GitHub account, or
   - Google account, or
   - Email
4. **Connect GitHub** (if using GitHub signup)
5. Authorize Render to access your repositories

---

## Step 4️⃣: Create Web Service on Render

1. On Render dashboard, click **"New +"** button
2. Select **"Web Service"**
3. Select **"Build and deploy from a Git repository"**
4. Click **"Connect"** next to your `voting-system` repository
5. Fill in the details:

```
Name:              voting-system
Environment:       PHP
Build Command:     echo "Build complete"
Start Command:     php -S 0.0.0.0:$PORT
Plan:              Free
```

6. Click **"Create Web Service"**
7. **Wait for deployment** (~2-3 minutes)

**Status indicators:**
- 🟡 Yellow = Building
- 🟢 Green = Live and running
- 🔴 Red = Error (check logs)

---

## Step 5️⃣: Create MySQL Database

1. On Render dashboard, click **"New +"** button
2. Select **"MySQL"**
3. Fill in the details:

```
Name:              voting-db
Database:          voting_system
Username:          root
Password:          [Generate strong password - save it!]
Region:            [Choose closest to you]
Plan:              Free
```

4. Click **"Create Database"**
5. **Wait for initialization** (~2-3 minutes)

**Save these details!**

---

## Step 6️⃣: Configure Environment Variables

1. Go to **Web Service** → **Environment** tab
2. Click **"Add Environment Variable"**
3. Add these variables:

### From MySQL Service:

1. Go to MySQL service page
2. Click **"Connect"** tab
3. Copy the **"Hostname"** (looks like: `mysql.render.com`)

### Add to Web Service Environment Variables:

```
DB_HOST        →  [Paste the Hostname from MySQL]
DB_PORT        →  3306
DB_USER        →  root
DB_PASSWORD    →  [The password you set for MySQL]
DB_NAME        →  voting_system
```

4. Click **"Save Changes"**
5. Web Service will auto-restart with new variables

---

## Step 7️⃣: Initialize Database

### Option A: Using MySQL Connection

1. Install **MySQL Workbench** (if not already installed)
   - Download from: https://dev.mysql.com/downloads/workbench/

2. On Render MySQL service page:
   - Click **"Connect"** tab
   - Copy the **"MYSQL_URL"** connection string

3. In MySQL Workbench:
   - Click **"MySQL Connections"** → **"+"**
   - Connection Name: `Render Voting DB`
   - Hostname: [From MYSQL_URL]
   - Username: `root`
   - Password: [Your MySQL password]
   - Port: `3306`
   - Click **"Test Connection"**

4. Once connected:
   - Open file: `/home/danmaina/Downloads/voting/kimati-voting-system/database.sql`
   - Copy all SQL code
   - Paste in MySQL Workbench
   - Execute (Ctrl+Enter or ⌘+Enter)

### Option B: Using Command Line

```bash
# Install mysql-client if not installed
# macOS: brew install mysql-client
# Linux: sudo apt-get install mysql-client
# Windows: Download MySQL Workbench

# Connect and run database.sql
mysql -h [HOSTNAME] -u root -p[PASSWORD] < database.sql
```

**Verify**: Check that tables were created:
```sql
SHOW TABLES;
```

---

## Step 8️⃣: Create Admin User

### Option A: Web Interface (Recommended)

1. Visit your live app: `https://voting-system.onrender.com`
2. Click **"Register"**
3. Create account:
   - Name: `Admin User`
   - Email: `admin@university.edu`
   - Password: `admin` (or your choice)
   - Registration #: `ADMIN001`
   - Year: `1`

4. Once registered, login
5. In browser console (F12), run this SQL via MySQL connection:

```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@university.edu';
```

### Option B: Direct MySQL (Fastest)

```bash
# Generate password hash
php -r "echo password_hash('admin', PASSWORD_BCRYPT);"
# Copy the output (looks like: $2y$12$...)

# Then run in MySQL:
INSERT INTO users (name, email, password, registration_number, year, role) 
VALUES ('Admin User', 'admin@university.edu', '[PASTE_HASH_HERE]', 'ADMIN001', 1, 'admin');
```

Or use the ready-made SQL:

```sql
INSERT INTO users (name, email, password, registration_number, year, role) 
VALUES ('Admin User', 'admin@university.edu', '$2y$12$drEGwWIcs8.TxBA5I5po4Oi0lP8TD4QyFiq3Wgm4..3fvGBUC3Tpi', 'ADMIN001', 1, 'admin');
```

---

## Step 9️⃣: Test Your Application

1. **Visit**: `https://voting-system.onrender.com`
2. **Test Login** (should see the homepage)
3. **Register** a new user (test voter)
4. **Login** with your credentials
5. **Go to Admin Dashboard** (if admin account)
6. **Test Features**:
   - ✓ Create an election
   - ✓ Add a coalition
   - ✓ Add candidates with images
   - ✓ Activate election
   - ✓ Vote (as regular user)
   - ✓ View results with charts

---

## 🔟 Troubleshooting

### "Cannot connect to database"
**Problem**: Database connection error  
**Solution**:
- Verify all environment variables are correct
- Check MySQL service is running (green status)
- Ensure IP whitelist is open (should be by default)

### "Blank page / 500 error"
**Problem**: Server error  
**Solution**:
- Check **Logs** tab on Web Service
- Scroll down to see error messages
- Common: Missing database initialization

### "Images not showing"
**Problem**: Uploaded images disappear on restart  
**Solution**:
- Expected on free tier (ephemeral storage)
- For production, use cloud storage (S3, Cloudinary)
- Database-stored image paths persist

### "Database shows empty tables"
**Problem**: Tables not created  
**Solution**:
- Ensure you ran `database.sql`
- Check MySQL connection worked
- Run: `SHOW TABLES;` to verify

---

## 📊 Deployment Checklist

- [ ] GitHub repository created
- [ ] Code pushed to GitHub
- [ ] Render.com account created
- [ ] GitHub connected to Render
- [ ] Web Service created and running (green status)
- [ ] MySQL Database created and running (green status)
- [ ] Environment variables set
- [ ] Database initialized (database.sql executed)
- [ ] Admin user created
- [ ] App accessible at https://voting-system.onrender.com
- [ ] Can login with admin@university.edu / admin
- [ ] Can register new users
- [ ] Can view elections and vote
- [ ] Charts display on results page
- [ ] Can upload candidate images

---

## 🎯 Success Indicators

### Web Service Status:
- URL available and responding
- Shows homepage without errors
- Login page works

### Database Status:
- Users can register
- Login succeeds
- Data persists after refresh

### Features Working:
- Voting functionality
- Charts on results page
- Image uploads (may not persist on free tier)
- Dark mode toggle
- Responsive design

---

## 🚀 You're Live!

Your voting system is now live on Render.com! 

**Share your app**:
- URL: `https://voting-system.onrender.com`
- Let users register and vote

---

## 💰 Free Tier Details

| Feature | Free Plan |
|---------|-----------|
| Web Service | 750 hrs/month |
| MySQL Storage | 1 GB |
| Bandwidth | Unlimited |
| SSL Certificate | Included |
| Auto-deploy | ✓ |
| Ephemeral Disk | ✓ (images reset) |

**Upgrade anytime** when you need:
- Persistent file storage
- More database space
- Better performance
- Custom domain

---

## 📞 Support

**Having issues?**

1. Check Render **Logs** tab (click Web Service → Logs)
2. Verify all environment variables
3. Confirm database is running
4. Check database.sql was executed
5. Review error messages in logs

**Still stuck?**
- Render Docs: https://render.com/docs
- PHP Docs: https://www.php.net/docs.php
- MySQL Docs: https://dev.mysql.com/doc/

---

## 🎉 Next Steps

1. Monitor your app for issues
2. Get feedback from users
3. Plan upgrades as needed
4. Expand features

**Congratulations! Your voting system is deployed!** 🚀
