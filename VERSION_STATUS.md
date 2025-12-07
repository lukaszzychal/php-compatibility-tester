# PHP and Framework Version Status

## Answers to Questions

### 1. Why did I use older PHP versions?

**Answer**: My training knowledge ends in April 2024, so I used versions that were stable and commonly used at that time (PHP 8.1, 8.2, 8.3). PHP 8.4 was released in November 2024, so it wasn't available at the time of my training.

**Which AI model has more current information?**

Models with updates (2024/2025):
- **Claude 3.5 Sonnet** (Anthropic) - updates to 2024
- **GPT-4 Turbo** (OpenAI) - updates to April 2024
- **GPT-4o** (OpenAI) - latest, updates to October 2024
- **Claude 3 Opus** - updates to 2024

For latest information (2025+):
- Use models with **web search** (e.g., models with internet access)
- Check official sources (php.net, framework documentation)
- Use real-time search tools

**Solution**: I've updated all places to the latest versions:
- ✅ PHP 8.4 added to all workflows and configurations
- ✅ PHP 8.5 added to docker-test.yml (already by you)
- ✅ Documentation updated

### 2. Which frameworks are ACTUALLY tested?

**Answer**: In CI/CD, the following frameworks are tested:

**Location**: `.github/workflows/self-test.yml`

1. **Laravel** (versions: 11.* LTS, 12.* Latest stable)
2. **Symfony** (versions: 7.4.* LTS, 8.0.* Latest stable)
3. **Slim** (versions: 4.* LTS, 5.* Latest stable)
4. **CodeIgniter** (version: 5.* Latest stable)
5. **CakePHP** (version: 5.* Latest stable)

**Why not all?**

- Testing all 9 frameworks would take too long in CI/CD
- Would require more resources (time, memory, network)
- Some frameworks require additional dependencies (e.g., Phalcon requires PHP extension)

**Configurable vs. Tested:**

**IMPORTANT**: The source code is **generic** - there are no hardcoded framework names. All frameworks are **configurable** (can be added to `.compatibility.yml`), but only some are **tested** in CI/CD.

| Framework | Configurable | Tested in CI | Status |
|-----------|--------------|--------------|--------|
| Laravel | ✅ | ✅ | Verified by operation |
| Symfony | ✅ | ✅ | Verified by operation |
| Slim | ✅ | ✅ | Verified by operation |
| CodeIgniter | ✅ | ✅ | Verified by operation |
| CakePHP | ✅ | ✅ | Verified by operation |
| Laminas | ✅ | ❌ | Example configuration only |
| Yii | ✅ | ❌ | Example configuration only |
| Lumen | ✅ | ❌ | Example configuration only |
| Phalcon | ✅ | ❌ | Example configuration only |

**Why are all "configurable"?**

Code in `src/FrameworkTester.php` is generic - it doesn't check framework name, only:
1. Uses `install_command` from configuration
2. Runs Composer create-project
3. Installs package
4. Runs test scripts

So **any framework** that has `composer create-project` can be used - there are no code limitations!

## How to add more frameworks for testing?

Edit `.github/workflows/self-test.yml`:

```yaml
framework-config:
  - framework: laravel
    version: '11.*'
    install_command: 'laravel/laravel'
    label: 'LTS'
  - framework: codeigniter
    version: '5.*'
    install_command: 'codeigniter4/appstarter'
    label: 'Latest stable'
```

## Current PHP Versions (2024/2025)

- **PHP 8.1** - LTS (support until 2025)
- **PHP 8.2** - LTS (support until 2026)
- **PHP 8.3** - Active Support (released November 2023)
- **PHP 8.4** - Active Support (released November 2024) ✅ Added
- **PHP 8.5** - Planned for 2025 ✅ Added to docker-test.yml

## Where are PHP versions?

Updated locations:

1. ✅ `.github/workflows/self-test.yml` - PHP 8.1-8.4
2. ✅ `.github/workflows/ci.yml` - PHP 8.1-8.4
3. ✅ `.github/workflows/docker-test.yml` - PHP 8.1-8.5 (already by you)
4. ✅ `templates/config/.compatibility.yml.example` - PHP 8.1-8.4
5. ✅ `src/Command/InitCommand.php` - PHP 8.1-8.4
6. ✅ `docker-compose.test.yml` - PHP 8.1-8.4
7. ✅ `README.md` - PHP 8.1-8.4
8. ✅ `docs/usage.md` - PHP 8.1-8.4

## Recommendations

1. **For production**: Use PHP 8.2 or 8.3 (LTS)
2. **For testing**: Add PHP 8.4 to all workflows
3. **For frameworks**: Consider adding more frameworks to `self-test.yml` if you have sufficient CI/CD resources

## Future Updates

To update versions in the future:

1. Check latest stable PHP versions on php.net
2. Update all files listed above
3. Test in CI/CD
4. Update documentation

