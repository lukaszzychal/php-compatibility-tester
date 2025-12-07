# PHP Compatibility Tester

![CI](https://github.com/lukaszzychal/php-compatibility-tester/actions/workflows/ci.yml/badge.svg)
![Docker Tests](https://github.com/lukaszzychal/php-compatibility-tester/actions/workflows/docker-test.yml/badge.svg)
![Self-Test](https://github.com/lukaszzychal/php-compatibility-tester/actions/workflows/self-test.yml/badge.svg)
![License](https://img.shields.io/github/license/lukaszzychal/php-compatibility-tester)
![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)
![Latest Release](https://img.shields.io/github/v/release/lukaszzychal/php-compatibility-tester?include_prereleases)
![Packagist Version](https://img.shields.io/packagist/v/lukaszzychal/php-compatibility-tester)
![Packagist Downloads](https://img.shields.io/packagist/dt/lukaszzychal/php-compatibility-tester)
![GitHub Issues](https://img.shields.io/github/issues/lukaszzychal/php-compatibility-tester)
![GitHub Pull Requests](https://img.shields.io/github/issues-pr/lukaszzychal/php-compatibility-tester)
![GitHub Contributors](https://img.shields.io/github/contributors/lukaszzychal/php-compatibility-tester)
![GitHub Stars](https://img.shields.io/github/stars/lukaszzychal/php-compatibility-tester?style=social)

A universal, reusable Composer package for testing PHP library compatibility with various frameworks (Laravel, Symfony, CodeIgniter, etc.) and PHP versions.

## What does this package do?

**In short**: This package automatically tests whether your PHP library works with different frameworks and PHP versions.

### The Problem It Solves

When you create a PHP library (e.g., `vendor/my-package`), you need to know:
- ✅ Does it work with Laravel 11 and 12?
- ✅ Does it work with Symfony 7 and 8?
- ✅ Does it work on PHP 8.1, 8.2, 8.3, 8.4?
- ✅ Are there any dependency conflicts?

Manually checking all these combinations is time-consuming and error-prone.

### How It Works (Step by Step)

1. **Creates temporary framework projects**
   - For each framework/version, it creates a fresh project (e.g., `composer create-project laravel/laravel`)

2. **Installs your library**
   - In each project, it installs your library via Composer

3. **Runs your tests**
   - Executes your custom test scripts (e.g., checks if classes can be autoloaded)

4. **Generates a report**
   - Creates a detailed report (Markdown/JSON/HTML) with all results

### Example Output

The report shows you:
- ✅ Laravel 11.* + PHP 8.2 - **WORKS**
- ❌ Laravel 12.* + PHP 8.1 - **FAILS** (requires PHP 8.2+)
- ✅ Symfony 7.4.* + PHP 8.3 - **WORKS**
- And so on...

### Real-World Analogy

Think of it like unit tests, but for compatibility:
- **Unit tests** = Does my code work correctly?
- **Compatibility Tester** = Does my code work in different environments?

### Who Is This For?

- **Library creators** - Want to know what your library works with
- **Package maintainers** - Need to support multiple frameworks
- **Teams** - Want to automatically check compatibility in CI/CD

### In One Sentence

**Automatically tests if your PHP library works with different frameworks and PHP versions, without manually checking every combination.**

## Features

- Test compatibility across multiple PHP versions (8.1+)
- Support for major PHP frameworks (Laravel, Symfony, CodeIgniter, Laminas, Yii, CakePHP, Slim, Lumen, Phalcon)
- YAML-based configuration
- CLI commands for easy testing
- Multiple report formats (Markdown, JSON, HTML)
- GitHub Actions integration
- Custom test scripts support

## Installation

Install via Composer:

```bash
composer require --dev lukaszzychal/php-compatibility-tester
```

## Quick Start

1. Initialize the configuration in your project:

```bash
vendor/bin/compatibility-tester init
```

This will create a `.compatibility.yml` configuration file and copy necessary templates.

2. Edit `.compatibility.yml` to configure your tests:

```yaml
package_name: "your-vendor/your-package"
php_versions: ['8.1', '8.2', '8.3', '8.4']
frameworks:
  laravel:
    versions: ['11.*', '12.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
```

3. Run compatibility tests:

```bash
vendor/bin/compatibility-tester test
```

4. Generate a report:

```bash
vendor/bin/compatibility-tester report --format=markdown --output=report.md
```

## Usage

### Initialize Configuration

```bash
vendor/bin/compatibility-tester init
```

Creates `.compatibility.yml` and copies template files to your project.

### Run Tests

```bash
# Run all tests
vendor/bin/compatibility-tester test

# Filter by framework
vendor/bin/compatibility-tester test --framework=laravel

# Filter by framework version
vendor/bin/compatibility-tester test --framework=laravel --version=11.*

# Filter by PHP version
vendor/bin/compatibility-tester test --php=8.3
```

### Generate Reports

```bash
# Generate Markdown report (default)
vendor/bin/compatibility-tester report

# Generate JSON report
vendor/bin/compatibility-tester report --format=json --output=report.json

# Generate HTML report
vendor/bin/compatibility-tester report --format=html --output=report.html
```

## Configuration

The `.compatibility.yml` file supports the following options:

- `package_name`: Your package name (e.g., "vendor/package")
- `php_versions`: Array of PHP versions to test
- `frameworks`: Framework configurations
  - `versions`: Framework versions to test
  - `install_command`: Command to create framework project
  - `php_min_version`: Minimum PHP version required
  - `php_min_version_X`: Version-specific PHP requirements
- `test_scripts`: Custom test scripts to run
- `github_actions`: GitHub Actions workflow settings

See [Configuration Documentation](docs/configuration.md) for detailed information.

## Supported Frameworks

- Laravel (11.x, 12.x)
- Symfony (7.4.x, 8.0.x)
- CodeIgniter (4.x, 5.x)
- Laminas (3.x)
- Yii (2.0.x)
- CakePHP (5.x)
- Slim (4.x, 5.x)
- Lumen (11.x)
- Phalcon (5.x)

## Requirements

- PHP 8.1 or higher
- Composer
- Symfony Console, Process, and YAML components (installed automatically)

## Documentation

- [Installation Guide](docs/installation.md)
- [Configuration Reference](docs/configuration.md)
- [Usage Guide](docs/usage.md)
- [GitHub MCP Server Integration](docs/GITHUB_MCP_INTEGRATION.md) - Automatyczne raportowanie i integracja z GitHub
- [Examples](docs/examples/)

## Links

- **Packagist**: [lukaszzychal/php-compatibility-tester](https://packagist.org/packages/lukaszzychal/php-compatibility-tester)
- **GitHub**: [lukaszzychal/php-compatibility-tester](https://github.com/lukaszzychal/php-compatibility-tester)
- **Issues**: [Report a bug or request a feature](https://github.com/lukaszzychal/php-compatibility-tester/issues)
- **Discussions**: [Ask questions and share ideas](https://github.com/lukaszzychal/php-compatibility-tester/discussions)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

- [Open an Issue](https://github.com/lukaszzychal/php-compatibility-tester/issues) to report bugs or suggest features
- [Start a Discussion](https://github.com/lukaszzychal/php-compatibility-tester/discussions) to ask questions or share ideas
- Submit a Pull Request to contribute code

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Author

Lukasz Zychal

