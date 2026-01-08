#!/bin/bash
# Deploy to Render.com - Complete Script

echo "🚀 Preparing University Voting System for Render.com deployment"
echo ""

# Step 1: Initialize Git
echo "Step 1: Initializing Git Repository..."
git init

# Step 2: Configure Git (if not already done)
echo ""
echo "Step 2: Configuring Git..."
echo "Note: Configure these with your GitHub credentials"
# Uncomment and customize:
# git config user.name "Your Name"
# git config user.email "your@email.com"

# Step 3: Add files
echo ""
echo "Step 3: Adding all files to Git..."
git add .

# Step 4: Create initial commit
echo ""
echo "Step 4: Creating initial commit..."
git commit -m "University Voting System - Ready for Render.com deployment

- Added image upload functionality for candidates
- Added chart visualization for results
- Configured for cloud deployment
- Updated for environment variables support"

echo ""
echo "✓ Git repository initialized!"
echo ""
echo "Next steps:"
echo "1. Create a repository on GitHub: https://github.com/new"
echo "2. Run these commands (replace YOUR_USERNAME with your GitHub username):"
echo ""
echo "   git remote add origin https://github.com/YOUR_USERNAME/voting-system.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""
echo "3. Then deploy on Render.com: https://render.com"
echo ""
echo "For detailed instructions, see: DEPLOY_QUICK_START.md"
