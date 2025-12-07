# Examples

This directory contains example configurations and usage patterns for PHP Compatibility Tester.

## GitHub MCP Server Integration

See [github-integration.php](github-integration.php) for examples of how to integrate PHP Compatibility Tester with GitHub MCP Server for automated reporting, issue creation, and workflow management.

For detailed integration guide, see [GITHUB_MCP_INTEGRATION.md](../GITHUB_MCP_INTEGRATION.md) and [GITHUB_MCP_QUICKSTART.md](../GITHUB_MCP_QUICKSTART.md).

## Example Configurations

### Minimal Configuration

A minimal `.compatibility.yml` for testing a simple package:

```yaml
package_name: "vendor/simple-package"
php_versions: ['8.1', '8.2']
frameworks:
  laravel:
    versions: ['11.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
```

### Multi-Framework Configuration

Testing across multiple frameworks:

```yaml
package_name: "vendor/multi-framework-package"
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
  
  slim:
    versions: ['4.*', '5.*']
    install_command: 'composer create-project slim/slim-skeleton'
    php_min_version: '8.1'
```

### With Custom Test Scripts

Configuration with custom test scripts:

```yaml
package_name: "vendor/tested-package"
php_versions: ['8.1', '8.2', '8.3']

frameworks:
  laravel:
    versions: ['11.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'

test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Verify package classes can be autoloaded'
  
  - name: service_provider
    script: 'tests/compatibility/check-service-provider.php'
    description: 'Test Laravel service provider registration'
  
  - name: facade
    script: 'tests/compatibility/check-facade.php'
    description: 'Test Laravel facade functionality'
```

## Example Test Scripts

### Basic Autoloading Test

`tests/compatibility/check-autoload.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

$classes = [
    'YourPackage\\Service',
    'YourPackage\\Facade',
    'YourPackage\\Provider',
];

foreach ($classes as $class) {
    if (!class_exists($class)) {
        echo "ERROR: Class {$class} not found\n";
        exit(1);
    }
}

echo "SUCCESS: All classes can be autoloaded\n";
exit(0);
```

### Laravel Service Provider Test

`tests/compatibility/check-service-provider.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if service is registered
if (!$app->bound('yourpackage.service')) {
    echo "ERROR: Service not registered\n";
    exit(1);
}

echo "SUCCESS: Service provider registered correctly\n";
exit(0);
```

### Symfony Service Test

`tests/compatibility/check-symfony-service.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;

// Bootstrap Symfony (adjust path based on Symfony version)
$kernel = require __DIR__ . '/../../config/bootstrap.php';
$kernel->boot();
$container = $kernel->getContainer();

// Check if service is registered
if (!$container->has('yourpackage.service')) {
    echo "ERROR: Service not registered in Symfony container\n";
    exit(1);
}

// Try to get the service
try {
    $service = $container->get('yourpackage.service');
    echo "SUCCESS: Symfony service registered and accessible\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: Service exists but cannot be retrieved: " . $e->getMessage() . "\n";
    exit(1);
}
```

### CodeIgniter Integration Test

`tests/compatibility/check-codeigniter.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap CodeIgniter
define('ENVIRONMENT', 'testing');
require __DIR__ . '/../../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

// Check if package classes can be loaded
if (!class_exists('YourPackage\\Service')) {
    echo "ERROR: Package class not found\n";
    exit(1);
}

// Test service instantiation
try {
    $service = new \YourPackage\Service();
    echo "SUCCESS: CodeIgniter integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Laminas Service Manager Test

`tests/compatibility/check-laminas.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laminas
$container = require __DIR__ . '/../../config/container.php';

// Check if service is registered
if (!$container->has('YourPackage\Service')) {
    echo "ERROR: Service not registered in Laminas service manager\n";
    exit(1);
}

// Get the service
try {
    $service = $container->get('YourPackage\Service');
    echo "SUCCESS: Laminas service manager integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Yii2 Component Test

`tests/compatibility/check-yii.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Yii2
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

// Check if component is registered
if (!Yii::$app->has('yourpackage')) {
    echo "ERROR: Component not registered in Yii2 application\n";
    exit(1);
}

// Get the component
try {
    $component = Yii::$app->get('yourpackage');
    echo "SUCCESS: Yii2 component integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

### CakePHP Plugin Test

`tests/compatibility/check-cakephp.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap CakePHP
use Cake\Core\Configure;
use Cake\Core\Plugin;

// Check if plugin is loaded
if (!Plugin::isLoaded('YourPackage')) {
    echo "ERROR: CakePHP plugin not loaded\n";
    exit(1);
}

// Check if service can be accessed
try {
    $service = \Cake\Core\Configure::read('YourPackage.service');
    echo "SUCCESS: CakePHP plugin integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Slim Container Test

`tests/compatibility/check-slim.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Slim
$app = require __DIR__ . '/../../src/app.php';
$container = $app->getContainer();

// Check if service is registered
if (!$container->has('yourpackage.service')) {
    echo "ERROR: Service not registered in Slim container\n";
    exit(1);
}

// Get the service
try {
    $service = $container->get('yourpackage.service');
    echo "SUCCESS: Slim container integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Lumen Service Provider Test

`tests/compatibility/check-lumen.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Lumen
$app = require __DIR__ . '/../../bootstrap/app.php';

// Check if service is registered
if (!$app->bound('yourpackage.service')) {
    echo "ERROR: Service not registered in Lumen\n";
    exit(1);
}

// Get the service
try {
    $service = $app->make('yourpackage.service');
    echo "SUCCESS: Lumen service provider integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

### Phalcon Service Test

`tests/compatibility/check-phalcon.php`:

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Phalcon
$di = new \Phalcon\Di\FactoryDefault();

// Check if service is registered
if (!$di->has('yourpackage.service')) {
    echo "ERROR: Service not registered in Phalcon DI\n";
    exit(1);
}

// Get the service
try {
    $service = $di->get('yourpackage.service');
    echo "SUCCESS: Phalcon DI integration works\n";
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
```

## Framework-Specific Configuration Examples

### Symfony Configuration

```yaml
package_name: "vendor/symfony-package"
php_versions: ['8.1', '8.2', '8.3']

frameworks:
  symfony:
    versions: ['7.4.*', '8.0.*']
    install_command: 'composer create-project symfony/symfony'
    php_min_version: '8.1'
    php_min_version_8: '8.2'

test_scripts:
  - name: symfony_service
    script: 'tests/compatibility/check-symfony-service.php'
    description: 'Test Symfony service container integration'
```

### CodeIgniter Configuration

```yaml
package_name: "vendor/codeigniter-package"
php_versions: ['8.1', '8.2']

frameworks:
  codeigniter:
    versions: ['4.*', '5.*']
    install_command: 'composer create-project codeigniter4/appstarter'
    php_min_version: '8.1'

test_scripts:
  - name: codeigniter_integration
    script: 'tests/compatibility/check-codeigniter.php'
    description: 'Test CodeIgniter integration'
```

### Multi-Framework with All Test Scripts

```yaml
package_name: "vendor/universal-package"
php_versions: ['8.1', '8.2', '8.3']

frameworks:
  laravel:
    versions: ['11.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
  
  symfony:
    versions: ['7.4.*']
    install_command: 'composer create-project symfony/symfony'
    php_min_version: '8.1'
  
  slim:
    versions: ['4.*']
    install_command: 'composer create-project slim/slim-skeleton'
    php_min_version: '8.1'

test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Verify package classes can be autoloaded'
  
  - name: laravel_service
    script: 'tests/compatibility/check-service-provider.php'
    description: 'Test Laravel service provider'
  
  - name: symfony_service
    script: 'tests/compatibility/check-symfony-service.php'
    description: 'Test Symfony service container'
  
  - name: slim_container
    script: 'tests/compatibility/check-slim.php'
    description: 'Test Slim container'
```

## Example Composer Scripts

Add to `composer.json`:

```json
{
  "scripts": {
    "compatibility": [
      "compatibility-tester test",
      "compatibility-tester report --format=markdown --output=compatibility-report.md"
    ],
    "compatibility:laravel": "compatibility-tester test --framework=laravel",
    "compatibility:php8.3": "compatibility-tester test --php=8.3",
    "compatibility:report": "compatibility-tester report --format=html --output=compatibility-report.html"
  }
}
```

Then run:

```bash
composer compatibility
composer compatibility:laravel
composer compatibility:php8.3
composer compatibility:report
```

