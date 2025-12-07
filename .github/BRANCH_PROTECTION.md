# Branch Protection Setup

## Protect Main Branch

To protect the `main` branch in GitHub:

1. Go to **Settings** → **Branches**
2. Under **Branch protection rules**, click **Add rule**
3. Configure:
   - **Branch name pattern**: `main`
   - **Require a pull request before merging**: ✅
   - **Require approvals**: 1 (optional but recommended)
   - **Require status checks to pass before merging**: ✅
     - Select: `CI / tests` (PHP 8.1, 8.2, 8.3, 8.4)
   - **Require conversation resolution before merging**: ✅ (optional)
   - **Do not allow bypassing the above settings**: ✅ (recommended)
   - **Restrict who can push to matching branches**: ✅ (optional, for teams)
4. Click **Create**

## Recommended Settings

- ✅ Require pull request reviews before merging
- ✅ Require status checks to pass before merging
- ✅ Require branches to be up to date before merging
- ✅ Do not allow force pushes
- ✅ Do not allow deletions
- ✅ Include administrators (optional)

## Status Checks

The following status checks should be required:
- `CI / tests (PHP 8.1)`
- `CI / tests (PHP 8.2)`
- `CI / tests (PHP 8.3)`
- `CI / tests (PHP 8.4)`
- `lint`

