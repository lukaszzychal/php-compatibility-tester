# Branch Protection Setup

## Protect Main Branch - Require Pull Requests Only

To protect the `main` branch so that **only merges through Pull Requests are allowed**:

### Quick Setup (Step by Step)

1. **Go to Repository Settings**:
   - Navigate to: https://github.com/lukaszzychal/php-compatibility-tester/settings/branches
   - Or: Repository → **Settings** → **Branches** (in left sidebar)

2. **Add Branch Protection Rule**:
   - Click **"Add rule"** or **"Add branch protection rule"** button

3. **Configure Branch Name Pattern**:
   - **Branch name pattern**: `main`

4. **Enable Pull Request Requirement** (CRITICAL):
   - ✅ **Require a pull request before merging** - CHECK THIS!
   - Under this section, configure:
     - ✅ **Require approvals**: `1` (recommended)
     - ✅ **Dismiss stale pull request approvals when new commits are pushed** (optional)
     - ✅ **Require review from Code Owners** (if you have CODEOWNERS file)

5. **Enable Status Checks** (Recommended):
   - ✅ **Require status checks to pass before merging**
   - ✅ **Require branches to be up to date before merging**
   - Select required status checks:
     - `CI / tests (PHP 8.1)`
     - `CI / tests (PHP 8.2)`
     - `CI / tests (PHP 8.3)`
     - `CI / tests (PHP 8.4)`
     - `lint`

6. **Additional Protection**:
   - ✅ **Require conversation resolution before merging** (optional but recommended)
   - ✅ **Require signed commits** (optional, for extra security)
   - ✅ **Require linear history** (optional, keeps clean git history)

7. **Prevent Direct Pushes**:
   - ✅ **Do not allow bypassing the above settings** - **CHECK THIS!**
   - ✅ **Restrict who can push to matching branches** (optional, for teams)
   - ✅ **Include administrators** - CHECK THIS to apply rules to admins too

8. **Prevent Force Push and Deletion**:
   - ✅ **Do not allow force pushes**
   - ✅ **Do not allow deletions**

9. **Save**:
   - Click **"Create"** or **"Save changes"** button

### What This Does

After configuration:
- ❌ **Direct push to `main`** → **BLOCKED**
- ✅ **Push to feature branch** → **ALLOWED**
- ✅ **Create Pull Request** → **ALLOWED**
- ✅ **Merge Pull Request** → **ALLOWED** (after approval and CI passes)
- ❌ **Force push to `main`** → **BLOCKED**
- ❌ **Delete `main` branch** → **BLOCKED**

## Minimum Required Settings (PR Only)

**To enforce PR-only merges, you MUST enable:**

1. ✅ **Require a pull request before merging** - **REQUIRED**
2. ✅ **Do not allow bypassing the above settings** - **REQUIRED**
3. ✅ **Do not allow force pushes** - **REQUIRED**
4. ✅ **Do not allow deletions** - **REQUIRED**

## Recommended Additional Settings

- ✅ Require pull request reviews before merging (1 approval)
- ✅ Require status checks to pass before merging
- ✅ Require branches to be up to date before merging
- ✅ Require conversation resolution before merging
- ✅ Include administrators (apply rules to admins too)

## Status Checks to Require

The following status checks should be required before merging:
- `CI / tests (PHP 8.1)`
- `CI / tests (PHP 8.2)`
- `CI / tests (PHP 8.3)`
- `CI / tests (PHP 8.4)`
- `lint`

## Verification

After setup, try to push directly to `main`:

```bash
git checkout main
git commit --allow-empty -m "Test direct push"
git push origin main
```

**Expected result**: ❌ Push should be **REJECTED** with error:
```
! [remote rejected] main -> main (protected branch hook declined)
```

## Workflow After Protection

1. **Create feature branch**:
   ```bash
   git checkout -b feature/my-feature
   ```

2. **Make changes and commit**:
   ```bash
   git add .
   git commit -m "Add new feature"
   ```

3. **Push to feature branch**:
   ```bash
   git push origin feature/my-feature
   ```

4. **Create Pull Request** on GitHub

5. **Wait for CI to pass** and **get approval**

6. **Merge Pull Request** (only way to get code into `main`)

## Troubleshooting

### "I need to push directly to main for hotfix"

**Solution**: Create a hotfix branch and PR:
```bash
git checkout -b hotfix/critical-fix
# Make changes
git push origin hotfix/critical-fix
# Create PR and merge quickly
```

### "I'm admin and still can't push"

**Solution**: Check "Include administrators" is enabled in branch protection rules

### "Status checks not showing"

**Solution**: 
1. Make sure workflows are running
2. Wait for at least one successful run
3. Status checks will appear in branch protection settings after first run

