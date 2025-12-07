# Installation Guide

## Requirements

Before installing PHP Compatibility Tester, ensure you have:

- PHP 8.1 or higher
- Composer installed and accessible in your PATH
- Git (for cloning repositories if needed)

## Installation Methods

### Via Composer (Recommended)

Install as a development dependency in your project:

```bash
composer require --dev lukaszzychal/php-compatibility-tester
```

### Global Installation

You can also install it globally:

```bash
composer global require lukaszzychal/php-compatibility-tester
```

After global installation, make sure the global Composer bin directory is in your PATH:

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

## Verify Installation

After installation, verify that the command is available:

```bash
vendor/bin/compatibility-tester --version
```

Or if installed globally:

```bash
compatibility-tester --version
```

## Next Steps

1. Initialize the configuration in your project:
   ```bash
   vendor/bin/compatibility-tester init
   ```

2. Edit the `.compatibility.yml` file to configure your tests

3. Run your first compatibility test:
   ```bash
   vendor/bin/compatibility-tester test
   ```

## Troubleshooting

### Command Not Found

If you get a "command not found" error:

1. Ensure Composer's vendor/bin directory is in your PATH
2. Run `composer install` to ensure dependencies are installed
3. Check that the `bin/compatibility-tester` file exists and is executable

### Permission Denied

If you get a permission error:

```bash
chmod +x vendor/bin/compatibility-tester
```

### Composer Autoload Issues

If you encounter autoload issues:

```bash
composer dump-autoload
```

