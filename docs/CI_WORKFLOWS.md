# CI/CD Workflows Documentation

This document describes the differences between CI/CD files in the project.

## Workflow Files

### 1. `ci.yml` - Main CI/CD Pipeline

**Purpose:** Basic tests, code quality, and security

**Checks:**
- ✅ PHPUnit tests (8.1, 8.2, 8.3, 8.4)
- ✅ PHPStan (static analysis)
- ✅ PHP-CS-Fixer (code style check)
- ✅ `composer.json` validation
- ✅ PHP syntax check
- ✅ Vulnerability scanning (Symfony Security Checker)
- ✅ Secret detection (TruffleHog)

**Environment:** Native PHP on GitHub Actions

**When it runs:**
- Push to `main` or `develop`
- Pull Request to `main` or `develop`

---

### 2. `docker-test.yml` - Docker Tests

**Purpose:** Verification of operation in isolated Docker containers

**Checks:**
- ✅ Building Docker image for each PHP version
- ✅ Running PHPUnit tests in container
- ✅ Running smoke tests in container
- ✅ Verification that all dependencies are correctly installed

**Environment:** Docker containers (isolation, production simulation)

**When it runs:**
- Push to `main` or `develop`
- Pull Request to `main` or `develop`
- Manually (`workflow_dispatch`)

**PHP Versions:** 8.1, 8.2, 8.3, 8.4

---

### 3. `self-test.yml` - Integration Tests with Frameworks

**Purpose:** Verification of package operation with real frameworks

**Checks:**
- ✅ Package installation in framework projects
- ✅ Compatibility with Laravel 11.* (LTS) and 12.* (Latest stable)
- ✅ Compatibility with Symfony 7.4.* (LTS) and 8.0.* (Latest stable)
- ✅ Compatibility with Slim 4.* (LTS) and 5.* (Latest stable)
- ✅ Compatibility with CodeIgniter 5.* (Latest stable)
- ✅ Compatibility with CakePHP 5.* (Latest stable)
- ✅ Compatibility report generation
- ✅ Autoloading tests in frameworks

**Environment:** Native PHP on GitHub Actions

**When it runs:**
- Push to `main` or `develop`
- Pull Request to `main` or `develop`
- Monthly on the 1st day at 2:00 AM (cron)

**Matrix:**
- PHP: 8.1, 8.2, 8.3, 8.4
- Frameworks: Laravel (LTS + Latest), Symfony (LTS + Latest), Slim (LTS + Latest), CodeIgniter, CakePHP

**Note:** Tests can take a long time (timeout: 30 minutes), so they use `continue-on-error: true`

---

## Comparison

| Feature | `ci.yml` | `docker-test.yml` | `self-test.yml` |
|---------|----------|-------------------|-----------------|
| **Test Type** | Unit + Quality | Docker Isolation | Integration |
| **Frameworks** | ❌ | ❌ | ✅ (Laravel, Symfony, Slim, CodeIgniter, CakePHP) |
| **Docker** | ❌ | ✅ | ❌ |
| **PHPStan** | ✅ | ❌ | ❌ |
| **Security Scan** | ✅ | ❌ | ❌ |
| **Execution Time** | ~5-10 min | ~5-10 min | ~20-30 min |
| **Frequency** | Every push/PR | Every push/PR | Push/PR + monthly |

---

## When to use which workflow?

- **`ci.yml`**: Basic tests on every code change
- **`docker-test.yml`**: Verification of operation in production environment (Docker)
- **`self-test.yml`**: End-to-end tests with real frameworks (longer, less frequent)

---

## Status Checks

All three workflows are required before merging PR (if configured as required status checks in branch protection settings).

