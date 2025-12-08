# CI/CD Documentation

This project uses GitHub Actions for continuous integration and deployment.

## Workflows

### CI Workflow (`.github/workflows/ci.yml`)

Runs on every push and pull request:

- **Tests**: PHPUnit tests on PHP 8.1, 8.2, 8.3
- **Linting**: PHP syntax validation and Composer validation
- **Code Quality**: PHPStan Fixer analysis with automatic fix suggestions (optional, non-blocking)
- **Code Style**: PHP-CS-Fixer checks (optional, non-blocking)

### Self-Test Workflow (`.github/workflows/self-test.yml`)

Tests the compatibility tester itself with real frameworks:

- Tests against Laravel, Symfony, and Slim
- Runs on multiple PHP versions (8.1, 8.2, 8.3)
- Creates test packages and verifies framework integration
- Generates test reports

### Security Workflow

- **Secret Scanning**: Automatically scans for exposed secrets
- **Security Checker**: Checks dependencies for known vulnerabilities
- **CodeQL**: Static code analysis for security issues

### Dependabot

Automatically creates pull requests for dependency updates:

- Weekly checks for Composer dependencies
- Automatic security updates
- Limited to 10 open PRs at a time

## Secret Protection

### Automated Scanning

The project uses multiple layers of secret protection:

1. **GitHub Secret Scanning**: Built-in GitHub feature that scans all commits
2. **TruffleHog**: Additional scanning in CI workflow
3. **Pre-commit hooks**: Local validation before commits
4. **Detect-secrets**: Baseline tracking for known false positives

### Protected Patterns

The following patterns are automatically detected:

- API keys and tokens
- AWS credentials
- GitHub tokens
- Private keys (RSA, EC, DSA, OpenSSH)
- Database credentials
- JWT tokens

### If You Accidentally Commit a Secret

1. **Immediately** rotate/revoke the exposed secret
2. Remove it from git history:
   ```bash
   git filter-branch --force --index-filter \
     "git rm --cached --ignore-unmatch path/to/secret" \
     --prune-empty --tag-name-filter cat -- --all
   ```
3. Force push (if already pushed):
   ```bash
   git push origin --force --all
   ```
4. Notify maintainers if pushed to public repository

## Local Development

### Pre-commit Hooks

Install pre-commit hooks for local validation:

```bash
pip install pre-commit
pre-commit install
```

This will automatically check for secrets before each commit.

### Update Secrets Baseline

If you have false positives, update the baseline:

```bash
detect-secrets scan --baseline .secrets.baseline
```

## CI/CD Best Practices

1. **Never commit secrets** - Use environment variables or GitHub Secrets
2. **Review CI logs** - Check for any warnings or errors
3. **Keep dependencies updated** - Review Dependabot PRs regularly
4. **Test locally first** - Run tests before pushing
5. **Use feature branches** - Don't push directly to main

## Environment Variables

For CI/CD, use GitHub Secrets for sensitive values:

- Go to Repository Settings → Secrets and variables → Actions
- Add secrets that can be used in workflows
- Never hardcode secrets in workflow files

## Troubleshooting

### PHPStan Fixer Issues

If you encounter issues with PHPStan Fixer (autoloader errors, binary not found, etc.), see:
- [PHPStan Fixer Troubleshooting Guide](TROUBLESHOOTING_PHPSTAN_FIXER.md)

### CI Fails Due to Secret Detection

If CI fails because of a false positive:

1. Add the pattern to `.secrets.baseline`
2. Update the baseline file
3. Commit the updated baseline

### Tests Fail in CI but Pass Locally

- Check PHP version differences
- Verify Composer dependencies are locked
- Check for environment-specific issues
- Review CI logs for specific error messages

