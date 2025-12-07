# Quick Setup Guide - Branch Protection

## One-Click Setup Instructions

### Step 1: Navigate to Branch Protection

**Direct link**: https://github.com/lukaszzychal/php-compatibility-tester/settings/branches

Or manually:
1. Go to repository: https://github.com/lukaszzychal/php-compatibility-tester
2. Click **Settings** (top menu)
3. Click **Branches** (left sidebar)

### Step 2: Add Protection Rule

1. Click **"Add rule"** button
2. In **"Branch name pattern"** field, type: `main`
3. Scroll down and check these **REQUIRED** options:

```
✅ Require a pull request before merging
   ✅ Require approvals: 1
   
✅ Require status checks to pass before merging
   ✅ Require branches to be up to date before merging
   
✅ Do not allow force pushes
✅ Do not allow deletions
✅ Do not allow bypassing the above settings
✅ Include administrators
```

4. Click **"Create"** button

### Step 3: Verify

Try pushing directly to main - it should be blocked!

```bash
git checkout main
git commit --allow-empty -m "Test"
git push origin main
# Should fail with: protected branch hook declined
```

## What Happens After Setup

✅ **Allowed**:
- Creating branches
- Pushing to feature branches
- Creating Pull Requests
- Merging Pull Requests (after approval)

❌ **Blocked**:
- Direct push to `main`
- Force push to `main`
- Deleting `main` branch
- Merging without PR

## Need Help?

See detailed instructions in [BRANCH_PROTECTION.md](BRANCH_PROTECTION.md)

