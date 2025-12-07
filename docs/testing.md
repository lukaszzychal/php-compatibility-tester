# Testing Documentation

This document describes the testing infrastructure for PHP Compatibility Tester.

## Test Structure

### Unit Tests (`tests/Unit/`)

Fast, isolated tests that don't require external dependencies:

- **`ConfigLoaderTest.php`**: Tests configuration loading and validation
- **`SmokeTest.php`**: Quick smoke tests for basic functionality

### Integration Tests (`tests/Integration/`)

Tests that verify integration with frameworks and external systems:

- **`FrameworkTesterTest.php`**: Tests framework project creation and package installation

### Test Fixtures (`tests/fixtures/`)

- **`test-package/`**: Minimal test package used for integration testing

## Running Tests

### Local Testing

Run all tests:

```bash
vendor/bin/phpunit
```

Run specific test suite:

```bash
# Unit tests only
vendor/bin/phpunit tests/Unit

# Integration tests only
vendor/bin/phpunit tests/Integration

# Smoke tests only
vendor/bin/phpunit tests/Unit/SmokeTest.php
```

### Docker Testing

Test in isolated Docker containers with different PHP versions:

```bash
# Build and test with PHP 8.1
docker build --build-arg PHP_VERSION=8.1 -f Dockerfile.test -t php-compatibility-tester:php81 .
docker run --rm -v $(pwd):/app php-compatibility-tester:php81 vendor/bin/phpunit

# Use docker-compose
docker-compose -f docker-compose.test.yml run test-php81 vendor/bin/phpunit
```

### CI/CD Testing

Tests run automatically on:

- Push to main/develop branches
- Pull requests
- Scheduled runs (weekly)

## Test Types

### Smoke Tests

Quick tests that verify basic functionality without requiring full framework installation:

- Configuration loading
- Report generation
- Basic class instantiation

These tests run fast and provide quick feedback during development.

### Integration Tests

Tests that verify the compatibility tester works with real frameworks:

- Framework project creation
- Package installation in frameworks
- Test script execution
- Report generation

**Note**: Integration tests may be skipped if:
- Composer is not available
- Network access is unavailable
- Framework installation fails

### Self-Tests

GitHub Actions workflow that tests the compatibility tester itself:

- Creates test packages
- Tests against real frameworks (Laravel, Symfony, Slim)
- Verifies end-to-end functionality
- Generates test reports

## Test Package

The test package (`tests/fixtures/test-package/`) is a minimal Composer package used for integration testing:

- Simple service class
- Basic autoloading test
- Compatible with all supported frameworks

## Writing Tests

### Adding Unit Tests

Create tests in `tests/Unit/`:

```php
<?php

namespace LukaszZychal\PhpCompatibilityTester\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }
}
```

### Adding Integration Tests

Create tests in `tests/Integration/`:

```php
<?php

namespace LukaszZychal\PhpCompatibilityTester\Tests\Integration;

use PHPUnit\Framework\TestCase;

class MyIntegrationTest extends TestCase
{
    public function testFrameworkIntegration(): void
    {
        // Test framework integration
        // May be skipped if dependencies unavailable
    }
}
```

### Best Practices

1. **Use descriptive test names**: Test names should clearly describe what they test
2. **Keep tests isolated**: Each test should be independent
3. **Use setUp/tearDown**: Clean up resources properly
4. **Skip when appropriate**: Use `markTestSkipped()` for optional dependencies
5. **Test edge cases**: Don't just test happy paths
6. **Mock external dependencies**: Use mocks for slow or unreliable dependencies

## Continuous Integration

### GitHub Actions Workflows

- **`ci.yml`**: Main CI pipeline (unit tests, linting, code quality)
- **`self-test.yml`**: Tests the tester with real frameworks
- **`docker-test.yml`**: Docker-based testing with multiple PHP versions

### Test Coverage

While we don't enforce strict coverage requirements, we aim for:

- High coverage of core functionality
- All public APIs tested
- Critical paths covered

## Troubleshooting

### Tests Fail Locally but Pass in CI

- Check PHP version differences
- Verify Composer dependencies are up to date
- Check for environment-specific issues

### Integration Tests Skipped

Integration tests are skipped when:
- Composer is not available
- Network access is unavailable
- Framework installation fails

This is expected behavior - integration tests are optional.

### Docker Tests Fail

- Ensure Docker is running
- Check Docker has enough resources
- Verify Dockerfile.test is correct
- Check for port conflicts

