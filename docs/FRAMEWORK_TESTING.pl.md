# Framework Testing Status

## Configurable vs. Tested Frameworks

**IMPORTANT**: The source code is **generic** - there are no hardcoded framework names. All frameworks are **configurable** (can be added to `.compatibility.yml`), but only some are **tested** in CI/CD.

### CONFIGURABLE Frameworks (can be configured in `.compatibility.yml`)

The package supports configuration for the following frameworks:

1. **Laravel** - `laravel/laravel`
2. **Symfony** - `symfony/symfony`
3. **CodeIgniter** - `codeigniter4/appstarter`
4. **Laminas** - `laminas/laminas-mvc-skeleton`
5. **Yii** - `yiisoft/yii2-app-basic`
6. **CakePHP** - `cakephp/app`
7. **Slim** - `slim/slim-skeleton`
8. **Lumen** - `laravel/lumen`
9. **Phalcon** - `phalcon/mvc`

### ACTUALLY TESTED Frameworks in CI/CD

**Location**: `.github/workflows/self-test.yml`

Currently in CI/CD, the following frameworks are tested:

1. **Laravel** (versions: 11.* LTS, 12.* Latest stable)
2. **Symfony** (versions: 7.4.* LTS, 8.0.* Latest stable)
3. **Slim** (versions: 4.* LTS, 5.* Latest stable)
4. **CodeIgniter** (version: 5.* Latest stable)
5. **CakePHP** (version: 5.* Latest stable)

**Why not all frameworks?**

- Testing all frameworks takes a lot of time
- Requires more CI/CD resources
- Some frameworks require additional dependencies (e.g., Phalcon requires PHP extension)

### How to add more frameworks for testing?

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

## PHP Versions

### Currently tested PHP versions

**Location**: `.github/workflows/self-test.yml` (line 18)

- PHP 8.1
- PHP 8.2
- PHP 8.3
- PHP 8.4

### Latest stable PHP versions (2024/2025)

- PHP 8.1 (Long Term Support - support until 2025)
- PHP 8.2 (Long Term Support - support until 2026)
- PHP 8.3 (Active Support)
- PHP 8.4 (Active Support - released in November 2024)
- PHP 8.5 (planned for 2025)

## Recommendations

1. **For production**: Use PHP 8.2 or 8.3 (LTS)
2. **For testing**: Add PHP 8.4 to CI/CD tests
3. **For frameworks**: Consider adding more frameworks to `self-test.yml` if you have sufficient CI/CD resources

## Version Updates

To update PHP versions in all places:

1. `.github/workflows/self-test.yml` - matrix php-version
2. `.github/workflows/ci.yml` - matrix php-version
3. `.github/workflows/docker-test.yml` - matrix php-version (already updated)
4. `templates/config/.compatibility.yml.example` - php_versions
5. `src/Command/InitCommand.php` - default php_versions
6. Documentation in `docs/`
