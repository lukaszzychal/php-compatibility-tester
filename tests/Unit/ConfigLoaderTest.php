<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester\Tests\Unit;

use LukaszZychal\PhpCompatibilityTester\ConfigLoader;
use LukaszZychal\PhpCompatibilityTester\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
class ConfigLoaderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/php-compatibility-tester-tests';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ConfigLoader::load
     */
    public function testLoadValidConfig(): void
    {
        $configPath = $this->tempDir . '/.compatibility.yml';
        $configContent = <<<YAML
package_name: "test/package"
php_versions: ['8.1', '8.2']
frameworks:
  laravel:
    versions: ['11.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
YAML;
        file_put_contents($configPath, $configContent);

        $loader = new ConfigLoader();
        $config = $loader->load($configPath);

        $this->assertArrayHasKey('package_name', $config);
        $this->assertEquals('test/package', $config['package_name']);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ConfigLoader::load
     */
    public function testLoadMissingFile(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Configuration file not found');

        $loader = new ConfigLoader();
        $loader->load('/nonexistent/file.yml');
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ConfigLoader::load
     */
    public function testLoadInvalidConfig(): void
    {
        $configPath = $this->tempDir . '/.compatibility.yml';
        file_put_contents($configPath, 'invalid: yaml: content: [');

        $this->expectException(ConfigurationException::class);

        $loader = new ConfigLoader();
        $loader->load($configPath);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ConfigLoader::load
     */
    public function testValidateMissingPackageName(): void
    {
        $configPath = $this->tempDir . '/.compatibility.yml';
        $configContent = <<<YAML
php_versions: ['8.1']
YAML;
        file_put_contents($configPath, $configContent);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('package_name');

        $loader = new ConfigLoader();
        $loader->load($configPath);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}

