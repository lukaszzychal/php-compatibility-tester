# Configuration Reference

The PHP Compatibility Tester uses a YAML configuration file (`.compatibility.yml`) to define test parameters.

## Configuration File Location

By default, the configuration file should be named `.compatibility.yml` and placed in your project root. You can specify a different path using the `--config` option.

## Configuration Structure

### Basic Configuration

```yaml
package_name: "vendor/package-name"
php_versions: ['8.1', '8.2', '8.3', '8.4']
```

### Package Name

**Required.** The name of your package as it appears in `composer.json`.

```yaml
package_name: "your-vendor/your-package"
```

### PHP Versions

**Required.** Array of PHP versions to test against.

```yaml
php_versions: ['8.1', '8.2', '8.3', '8.4']
```

## Framework Configuration

### Framework Structure

```yaml
frameworks:
  framework_name:
    versions: ['version1', 'version2']
    install_command: 'composer create-project framework/package'
    php_min_version: '8.1'
    php_min_version_X: '8.2'  # Version-specific requirement
    note: 'Optional note about the framework'
```

### Framework Options

- **versions** (required): Array of framework versions to test. Supports wildcards (e.g., `11.*`, `7.4.*`)
- **install_command** (required): Composer command to create a new framework project
- **php_min_version** (required): Minimum PHP version required for the framework
- **php_min_version_X** (optional): Version-specific PHP requirement (replace X with version identifier)
- **note** (optional): Additional information about the framework

### Example Framework Configurations

#### Laravel

```yaml
laravel:
  versions: ['11.*', '12.*']
  install_command: 'composer create-project laravel/laravel'
  php_min_version: '8.1'
  php_min_version_12: '8.2'  # Laravel 12 requires PHP 8.2+
```

#### Symfony

```yaml
symfony:
  versions: ['7.4.*', '8.0.*']
  install_command: 'composer create-project symfony/symfony'
  php_min_version: '8.1'
  php_min_version_8: '8.2'  # Symfony 8.0 requires PHP 8.2+
```

## Test Scripts

Define custom test scripts to run in framework projects:

```yaml
test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Test class autoloading'
  - name: basic_functionality
    script: 'tests/compatibility/check-basic.php'
    description: 'Test basic library functionality'
```

### Test Script Options

- **name** (required): Unique identifier for the test
- **script** (required): Path to the PHP script to execute (relative to framework project root)
- **description** (optional): Human-readable description of the test

## GitHub Actions

Configure GitHub Actions workflow behavior:

```yaml
github_actions:
  schedule: '0 2 * * 1'  # Cron expression (weekly on Monday at 2 AM)
  on_push: true          # Run on push events
  paths:                 # Files that trigger the workflow
    - 'composer.json'
    - 'composer.lock'
```

## Complete Example

```yaml
package_name: "mycompany/mypackage"
php_versions: ['8.1', '8.2', '8.3']

frameworks:
  laravel:
    versions: ['11.*', '12.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
    php_min_version_12: '8.2'
  
  symfony:
    versions: ['7.4.*', '8.0.*']
    install_command: 'composer create-project symfony/symfony'
    php_min_version: '8.1'
    php_min_version_8: '8.2'

test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Verify package classes can be autoloaded'
  
  - name: integration
    script: 'tests/compatibility/check-integration.php'
    description: 'Test package integration with framework'

github_actions:
  schedule: '0 2 * * 1'
  on_push: true
  paths:
    - 'composer.json'
    - 'composer.lock'
```

## Validation

The configuration file is validated when loaded. Common validation errors:

- Missing required fields
- Invalid array structures
- Missing framework configuration fields
- Invalid test script definitions

If validation fails, you'll receive a clear error message indicating what needs to be fixed.

