# Usage Guide

This guide covers how to use PHP Compatibility Tester in your projects.

## Initial Setup

### 1. Initialize Configuration

Run the init command in your project root:

```bash
vendor/bin/compatibility-tester init
```

This will:
- Create `.compatibility.yml` configuration file
- Copy PHPUnit test templates
- Set up GitHub Actions workflow template
- Copy test script templates

### 2. Configure Your Tests

Edit `.compatibility.yml` to match your package:

```yaml
package_name: "your-vendor/your-package"
php_versions: ['8.1', '8.2', '8.3', '8.4']
frameworks:
  laravel:
    versions: ['11.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
```

## Running Tests

### Basic Usage

Run all configured tests:

```bash
vendor/bin/compatibility-tester test
```

### Filtering Tests

Filter by framework:

```bash
vendor/bin/compatibility-tester test --framework=laravel
```

Filter by framework version:

```bash
vendor/bin/compatibility-tester test --framework=laravel --version=11.*
```

Filter by PHP version:

```bash
vendor/bin/compatibility-tester test --php=8.3
```

Combine filters:

```bash
vendor/bin/compatibility-tester test --framework=laravel --version=11.* --php=8.3
```

### Custom Configuration File

Use a different configuration file:

```bash
vendor/bin/compatibility-tester test --config=my-config.yml
```

## Generating Reports

### Markdown Report (Default)

```bash
vendor/bin/compatibility-tester report
```

Output to file:

```bash
vendor/bin/compatibility-tester report --output=report.md
```

### JSON Report

```bash
vendor/bin/compatibility-tester report --format=json --output=report.json
```

### HTML Report

```bash
vendor/bin/compatibility-tester report --format=html --output=report.html
```

## Integration with Composer Scripts

Add to your `composer.json`:

```json
{
  "scripts": {
    "compatibility-test": "compatibility-tester test",
    "compatibility-report": "compatibility-tester report"
  }
}
```

Then run:

```bash
composer compatibility-test
composer compatibility-report
```

## GitHub Actions Integration

The init command creates a GitHub Actions workflow template. After initialization, you'll find it at:

`.github/workflows/compatibility-tests.yml`

The workflow will:
- Run tests on push and pull requests
- Test against multiple PHP versions
- Generate and upload reports as artifacts

Customize the workflow as needed for your project.

## Custom Test Scripts

Create test scripts in your project (e.g., `tests/compatibility/check-autoload.php`):

```php
<?php

// Test that your package classes can be autoloaded
if (!class_exists('YourPackage\\YourClass')) {
    echo "ERROR: Class not found\n";
    exit(1);
}

echo "SUCCESS: Classes can be autoloaded\n";
exit(0);
```

Reference them in `.compatibility.yml`:

```yaml
test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Test class autoloading'
```

## Best Practices

1. **Start Small**: Begin with a few PHP versions and one framework, then expand
2. **Use Wildcards**: Use version wildcards (e.g., `11.*`) to test multiple minor versions
3. **Version-Specific Requirements**: Use `php_min_version_X` for frameworks with version-specific PHP requirements
4. **Custom Scripts**: Create focused test scripts that verify specific functionality
5. **Regular Testing**: Set up scheduled GitHub Actions runs to catch compatibility issues early
6. **Documentation**: Keep your `.compatibility.yml` well-documented with notes

## Troubleshooting

### Tests Fail to Run

- Check that Composer is installed and in PATH
- Verify framework install commands work manually
- Ensure PHP versions are available on your system

### Framework Projects Not Created

- Check disk space (framework projects can be large)
- Verify network connectivity (Composer needs to download packages)
- Check Composer cache and credentials

### Package Installation Fails

- Ensure your package has a valid `composer.json`
- Check that the package name in `.compatibility.yml` matches your `composer.json`
- Verify package dependencies are compatible

### Reports Not Generated

- Ensure tests have been run first
- Check file permissions for output directory
- Verify the output format is supported (markdown, json, html)

## Examples

See the [Examples](examples/) directory for complete working examples.

