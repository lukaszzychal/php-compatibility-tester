# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-12-08

### Fixed

- **Symfony Console 7.x compatibility**: Added explicit `setName()` to all commands (InitCommand, TestCommand, ReportCommand)
  - Required for Symfony Console 7.x compatibility
  - Maintains backward compatibility with Symfony Console 6.x
- **PHP 8.4 deprecation warnings**: Fixed implicit nullable syntax by using explicit nullable types (`?string`)
  - Applied to `InitCommand::__construct()`, `CompatibilityTester::__construct()`, and `FrameworkTester::__construct()`
- **CI workflow**: Added `phpstan/phpstan` as direct dev dependency
  - Ensures PHPStan binary is available in CI workflows

### Changed

- **BREAKING**: `TestCommand`: `--version` option renamed to `--framework-version`
  - Prevents conflict with Symfony Console's built-in `--version` option
  - **Migration**: Update all scripts using `--version` to use `--framework-version` instead

## [1.0.1] - 2025-12-07

### Changed

- Replaced `phpstan/phpstan` with `lukaszzychal/phpstan-fixer` for automatic PHPStan error fixes

## [1.0.0] - 2025-01-07

### Added

- Initial release of PHP Compatibility Tester
- CLI commands: `init`, `test`, `report`
- Support for multiple PHP frameworks (Laravel, Symfony, CodeIgniter, Laminas, Yii, CakePHP, Slim, Lumen, Phalcon)
- YAML-based configuration system
- Multiple report formats (Markdown, JSON, HTML)
- Framework compatibility testing
- Composer dependency resolution testing
- Custom test scripts support
- GitHub Actions workflow templates
- PHPUnit test templates
- Comprehensive documentation

### Features

- Test compatibility across multiple PHP versions
- Framework-specific version testing
- Version-specific PHP requirements
- Filtering options for tests
- Progress indicators and colored output
- Detailed error reporting

