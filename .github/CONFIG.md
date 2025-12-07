# GitHub Configuration

This directory contains GitHub-specific configuration files.

## Files

- **`workflows/`**: GitHub Actions workflows
  - `ci.yml`: Main CI pipeline (tests, linting, code quality)
  - `self-test.yml`: Tests the compatibility tester with real frameworks
  - `codeql.yml`: Security code analysis

- **`dependabot.yml`**: Automated dependency updates configuration

- **`secret-scanning.yml`**: Custom patterns for secret detection

- **`SECURITY.md`**: Security policy and vulnerability reporting

## Workflows

See [CI/CD Documentation](../docs/ci-cd.md) for detailed information about workflows.

## Secret Protection

This repository uses multiple layers of secret protection:

1. GitHub's built-in secret scanning
2. TruffleHog in CI workflows
3. Pre-commit hooks (see `.pre-commit-config.yaml`)
4. Detect-secrets baseline (see `.secrets.baseline`)

Never commit secrets, API keys, or credentials to this repository.

