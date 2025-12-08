# ✅ RESOLVED: Binary script fails when package is installed as dependency

## Status: ✅ Fixed in v1.0.3

**Update (2025-12-08):** This issue has been resolved in version **v1.0.3** (commit: `9bb7802dc65c094ea506472f12ad5a62920d5f1e`). The package now uses dynamic autoloader detection, which works correctly both when installed standalone and as a dependency.

## Original Problem Description

The `phpstan-fixer` binary script failed to load the autoloader when the package was installed as a Composer dependency in another project. The script tried to load `vendor/autoload.php` from the package's own directory, which doesn't exist when installed via Composer.

## Original Error Message

```
Warning: require_once(/path/to/project/vendor/lukaszzychal/phpstan-fixer/bin/../vendor/autoload.php): 
Failed to open stream: No such file or directory in 
/path/to/project/vendor/lukaszzychal/phpstan-fixer/bin/phpstan-fixer on line 21

Fatal error: Uncaught Error: Failed opening required 
'/path/to/project/vendor/lukaszzychal/phpstan-fixer/bin/../vendor/autoload.php' 
(include_path='.:') in 
/path/to/project/vendor/lukaszzychal/phpstan-fixer/bin/phpstan-fixer:21
```

## Solution Implemented

The fix implements the **Alternative Solution** (dynamic autoloader detection) that was proposed in the original issue. The binary script now traverses up the directory tree to find the autoloader, making it work in all installation scenarios:

```php
// Find the autoloader by traversing up the directory tree
// This works both when package is standalone and when installed as dependency
$autoloader = null;
$dir = __DIR__;
$maxDepth = 10; // Prevent infinite loops

for ($i = 0; $i < $maxDepth; $i++) {
    $possiblePath = $dir . '/vendor/autoload.php';
    if (file_exists($possiblePath)) {
        $autoloader = $possiblePath;
        break;
    }
    
    $parentDir = dirname($dir);
    if ($parentDir === $dir) {
        // Reached filesystem root
        break;
    }
    $dir = $parentDir;
}

if ($autoloader === null) {
    fwrite(STDERR, "Error: Could not find autoload.php.\n");
    fwrite(STDERR, "Please run 'composer install' to install dependencies.\n");
    exit(1);
}

require_once $autoloader;
```

## Verification

✅ **Tested and confirmed working:**
- Binary loads correctly when installed as dependency
- No autoloader errors
- All functionality works as expected
- Backward compatible (works in standalone mode too)

## How to Update

If you're experiencing this issue, update to the latest version:

```bash
composer update lukaszzychal/phpstan-fixer
```

## Original Issue Details (for reference)

### Steps to Reproduce (Original)

1. Create a new PHP project or use an existing one
2. Install `phpstan-fixer` as a dev dependency:
   ```bash
   composer require --dev lukaszzychal/phpstan-fixer
   ```
3. Try to run the binary:
   ```bash
   vendor/bin/phpstan-fixer --help
   ```
4. The error occurred immediately

### Root Cause (Original)

The binary script at `bin/phpstan-fixer` line 21 used:
```php
require_once __DIR__ . '/../vendor/autoload.php';
```

This assumed the package has its own `vendor` directory, which is only true when:
- The package is installed standalone (not as a dependency)
- The package is in development mode with its own dependencies

When installed as a dependency via Composer, the package's dependencies are flattened into the parent project's `vendor` directory, so `vendor/lukaszzychal/phpstan-fixer/vendor/` doesn't exist.

### Environment (Original)

- **Package Version**: v1.0.2 (affected)
- **Fixed Version**: v1.0.3
- **PHP Version**: 8.4.7
- **Composer Version**: 2.8.11
- **OS**: macOS 25.0.0 (Darwin Kernel, ARM64)
- **Installation Method**: Composer dependency (`composer require --dev`)

---

**Note:** This file is kept for historical reference. The issue has been resolved and the package works correctly in all installation scenarios.
