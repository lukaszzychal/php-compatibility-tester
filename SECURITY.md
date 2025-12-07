# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability, please **DO NOT** open a public issue. Instead, please report it privately:

1. **Email**: Send details to the maintainer (check package metadata)
2. **GitHub Security Advisory**: Use GitHub's private vulnerability reporting feature

### What to Include

- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

## Security Best Practices

### For Users

- Always use the latest stable version
- Review dependencies regularly
- Use Composer's security audit: `composer audit`
- Never commit secrets or API keys to your repository

### For Contributors

- Never commit secrets, API keys, or credentials
- Use environment variables for sensitive configuration
- Review pull requests for security issues
- Follow secure coding practices

## Secret Management

This project uses automated secret scanning to prevent accidental commits of sensitive information:

- **GitHub Secret Scanning**: Automatically scans commits
- **Pre-commit hooks**: Local validation before commits
- **CI/CD checks**: Automated scanning in pull requests

If you accidentally commit a secret:

1. **Immediately** rotate/revoke the exposed secret
2. Remove it from git history
3. Notify the maintainers if it was pushed to a public repository

## Dependencies

We regularly update dependencies to address security vulnerabilities. Use Dependabot alerts to stay informed about security updates.

