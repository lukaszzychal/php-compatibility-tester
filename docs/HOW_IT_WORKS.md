# How It Works

This document explains how the `php-compatibility-tester` library verifies your package's compatibility with different frameworks and PHP versions.

## Overview

The compatibility tester works by:

1. **Creating temporary framework projects** for each framework version you want to test
2. **Installing your package** into each framework project
3. **Running test scripts** to verify your package works correctly
4. **Generating reports** with the test results

## Step-by-Step Process

### 1. Configuration Loading

When you run `compatibility-tester test`, the tool first loads your `.compatibility.yml` configuration file:

```yaml
package_name: "vendor/package-name"
php_versions: ['8.1', '8.2', '8.3', '8.4']
frameworks:
  laravel:
    versions: ['11.*', '12.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
```

The `ConfigLoader` class validates this configuration and ensures all required fields are present.

### 2. Framework Project Creation

For each framework and version combination, the `FrameworkTester` class:

1. **Creates a temporary directory** for the framework project:
   ```
   /tmp/php-compatibility-tester/laravel-11-x-8.1/
   ```

2. **Runs the framework's install command**:
   ```bash
   composer create-project laravel/laravel /tmp/php-compatibility-tester/laravel-11-x-8.1
   ```

3. **Verifies the project was created successfully**

### 3. Package Installation

After the framework project is created, the tester:

1. **Adds your package as a local repository** in the framework's `composer.json`:
   ```json
   {
     "repositories": [
       {
         "type": "path",
         "url": "/path/to/your/package"
       }
     ]
   }
   ```

2. **Installs your package** using Composer:
   ```bash
   composer require vendor/package-name:dev-master
   ```

3. **Checks for installation errors** (dependency conflicts, PHP version incompatibilities, etc.)

### 4. Test Script Execution

For each test script defined in your configuration:

```yaml
test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Test class autoloading'
```

The tester:

1. **Locates the test script** (relative to your package root or absolute path)
2. **Runs the script** in the framework project context:
   ```bash
   php tests/compatibility/check-autoload.php
   ```
3. **Captures the output** and exit code
4. **Records success/failure** in the test results

### 5. Result Collection

All test results are collected into an array:

```php
[
  'success' => true/false,
  'error' => null or error message,
  'output' => 'command output',
  'framework' => 'laravel',
  'framework_version' => '11.*',
  'php_version' => '8.1',
  'test_results' => [
    'autoloading' => [
      'success' => true,
      'output' => 'SUCCESS: All classes can be autoloaded',
      ...
    ]
  ]
]
```

### 6. Report Generation

The `ReportGenerator` class creates reports in different formats:

- **Markdown**: Human-readable format for documentation
- **JSON**: Machine-readable format for CI/CD integration
- **HTML**: Visual format for web viewing

## Example: Testing Laravel 11 with PHP 8.3

Here's what happens when you test your package with Laravel 11 and PHP 8.3:

1. **Configuration is loaded** from `.compatibility.yml`
2. **Temporary directory is created**: `/tmp/php-compatibility-tester/laravel-11-x-8.3/`
3. **Laravel 11 project is created**:
   ```bash
   composer create-project laravel/laravel /tmp/php-compatibility-tester/laravel-11-x-8.3
   ```
4. **Your package is added to composer.json**:
   ```json
   {
     "repositories": [
       {
         "type": "path",
         "url": "/path/to/your/package"
       }
     ],
     "require": {
       "vendor/package-name": "dev-master"
     }
   }
   ```
5. **Composer installs your package**:
   ```bash
   composer require vendor/package-name:dev-master
   ```
6. **Test scripts are executed**:
   ```bash
   php tests/compatibility/check-autoload.php
   ```
7. **Results are collected** and added to the report

## Composer Dependency Testing

In addition to framework testing, the `ComposerTester` class tests dependency resolution:

1. **Creates a temporary composer.json** with your package as a dependency
2. **Tests dependency resolution** for each PHP version
3. **Detects conflicts** before running framework tests

This helps catch dependency issues early, before spending time on framework project creation.

## Filtering

You can filter tests using command-line options:

```bash
# Test only Laravel
compatibility-tester test --framework=laravel

# Test only PHP 8.3
compatibility-tester test --php=8.3

# Test Laravel 11.* with PHP 8.3
compatibility-tester test --framework=laravel --version=11.* --php=8.3
```

The filtering happens in the `CompatibilityTester::runTests()` method, which skips non-matching combinations.

## Error Handling

The tester handles various error scenarios:

- **Framework project creation fails**: Error is recorded, test continues with next framework
- **Package installation fails**: Error is recorded with detailed output
- **Test script fails**: Individual test failure is recorded, other tests continue
- **PHP version mismatch**: Test is skipped with a clear message

All errors are included in the final report, so you can see exactly what failed and why.

## Cleanup

By default, temporary framework projects are **not** automatically deleted. This allows you to:

- Inspect failed installations
- Debug issues manually
- Reuse projects for faster testing

You can manually clean up using:
```bash
rm -rf /tmp/php-compatibility-tester/
```

Or implement cleanup in your CI/CD pipeline after reports are generated.

## Performance Considerations

- **Framework project creation** is the slowest step (can take 1-5 minutes per framework)
- **Package installation** is relatively fast (usually < 30 seconds)
- **Test script execution** is very fast (usually < 5 seconds)

The tester uses `continue-on-error: true` in CI/CD to ensure all tests run even if some fail, giving you a complete picture of compatibility.

