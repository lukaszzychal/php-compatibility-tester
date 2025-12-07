<?php

declare(strict_types=1);

require __DIR__ . '/../../../vendor/autoload.php';

$classes = [
    'Test\\CompatibilityPackage\\TestService',
];

foreach ($classes as $class) {
    if (!class_exists($class)) {
        echo "ERROR: Class {$class} not found\n";
        exit(1);
    }
}

// Test instantiation
try {
    $service = new \Test\CompatibilityPackage\TestService();
    if ($service->test() !== true) {
        echo "ERROR: Service test method failed\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "ERROR: Failed to instantiate service: " . $e->getMessage() . "\n";
    exit(1);
}

echo "SUCCESS: All classes can be autoloaded and instantiated\n";
exit(0);

