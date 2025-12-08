<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester\Tests\Unit;

use LukaszZychal\PhpCompatibilityTester\ConfigLoader;
use LukaszZychal\PhpCompatibilityTester\CompatibilityTester;
use LukaszZychal\PhpCompatibilityTester\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Smoke tests for basic functionality verification.
 * These tests are quick and don't require full framework installation.
 *
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
class SmokeTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/php-compatibility-tester-smoke-tests';
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
    public function testConfigLoaderCanLoadValidConfig(): void
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
        $this->assertArrayHasKey('php_versions', $config);
        $this->assertArrayHasKey('frameworks', $config);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ConfigLoader::load
     */
    public function testConfigLoaderThrowsExceptionForMissingFile(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Configuration file not found');

        $loader = new ConfigLoader();
        $loader->load('/nonexistent/file.yml');
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::__construct
     */
    public function testCompatibilityTesterCanBeInstantiated(): void
    {
        $configPath = $this->tempDir . '/.compatibility.yml';
        $configContent = <<<YAML
package_name: "test/package"
php_versions: ['8.1']
YAML;
        file_put_contents($configPath, $configContent);

        $tester = new CompatibilityTester($configPath, __DIR__);
        $this->assertInstanceOf(CompatibilityTester::class, $tester);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::__construct
     */
    public function testCompatibilityTesterThrowsExceptionForInvalidConfig(): void
    {
        $configPath = $this->tempDir . '/.compatibility.yml';
        file_put_contents($configPath, 'invalid: yaml: content: [');

        $this->expectException(ConfigurationException::class);

        new CompatibilityTester($configPath, __DIR__);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ReportGenerator::generate
     */
    public function testReportGeneratorCanGenerateMarkdown(): void
    {
        $generator = new \LukaszZychal\PhpCompatibilityTester\ReportGenerator();
        $generator->setResults([
            [
                'success' => true,
                'framework' => 'laravel',
                'framework_version' => '11.*',
                'php_version' => '8.1',
            ],
        ]);

        $report = $generator->generate('markdown');
        $this->assertStringContainsString('PHP Compatibility Test Report', $report);
        $this->assertStringContainsString('laravel', $report);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ReportGenerator::generate
     */
    public function testReportGeneratorCanGenerateJson(): void
    {
        $generator = new \LukaszZychal\PhpCompatibilityTester\ReportGenerator();
        $generator->setResults([
            [
                'success' => true,
                'php_version' => '8.1',
            ],
        ]);

        $report = $generator->generate('json');
        $data = json_decode($report, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('results', $data);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\ReportGenerator::generate
     */
    public function testReportGeneratorCanGenerateHtml(): void
    {
        $generator = new \LukaszZychal\PhpCompatibilityTester\ReportGenerator();
        $generator->setResults([
            [
                'success' => true,
                'php_version' => '8.1',
            ],
        ]);

        $report = $generator->generate('html');
        $this->assertStringContainsString('<!DOCTYPE html>', $report);
        $this->assertStringContainsString('PHP Compatibility Test Report', $report);
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

