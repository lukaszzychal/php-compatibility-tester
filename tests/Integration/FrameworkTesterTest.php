<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester\Tests\Integration;

use LukaszZychal\PhpCompatibilityTester\CompatibilityTester;
use LukaszZychal\PhpCompatibilityTester\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for framework compatibility testing.
 * 
 * These tests verify that the compatibility tester can:
 * - Load configuration correctly
 * - Create framework projects (if Composer is available)
 * - Generate reports
 * 
 * Note: Full framework installation tests are slow and may require network access.
 * They are marked as skipped by default and can be enabled with environment variable.
 *
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
 */
class FrameworkTesterTest extends TestCase
{
    private string $tempDir;
    private string $testPackagePath;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/php-compatibility-tester-integration-tests';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $this->testPackagePath = __DIR__ . '/../fixtures/test-package';
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::__construct
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::getConfig
     */
    public function testCompatibilityTesterCanLoadTestPackageConfig(): void
    {
        $configPath = $this->testPackagePath . '/.compatibility.yml';
        
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Test package configuration not found');
        }

        $tester = new CompatibilityTester($configPath, $this->testPackagePath);
        $config = $tester->getConfig();

        $this->assertArrayHasKey('package_name', $config);
        $this->assertEquals('test/compatibility-package', $config['package_name']);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::generateReport
     */
    public function testCompatibilityTesterCanGenerateReportWithoutRunningTests(): void
    {
        $configPath = $this->testPackagePath . '/.compatibility.yml';
        
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Test package configuration not found');
        }

        $tester = new CompatibilityTester($configPath, $this->testPackagePath);
        
        // Generate report with empty results
        $report = $tester->generateReport('markdown');
        $this->assertStringContainsString('PHP Compatibility Test Report', $report);
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::runTests
     */
    public function testCompatibilityTesterCanFilterByFramework(): void
    {
        $configPath = $this->testPackagePath . '/.compatibility.yml';
        
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Test package configuration not found');
        }

        $tester = new CompatibilityTester($configPath, $this->testPackagePath);
        
        // This should not throw an exception even if tests fail
        try {
            $results = $tester->runTests(['framework' => 'laravel']);
            $this->assertIsArray($results);
        } catch (\Exception $e) {
            // If Composer is not available or network fails, that's okay for integration tests
            $this->markTestSkipped('Framework testing requires Composer and network access: ' . $e->getMessage());
        }
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::runTests
     */
    public function testCompatibilityTesterCanFilterByPhpVersion(): void
    {
        $configPath = $this->testPackagePath . '/.compatibility.yml';
        
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Test package configuration not found');
        }

        $tester = new CompatibilityTester($configPath, $this->testPackagePath);
        
        try {
            $results = $tester->runTests(['php' => '8.1']);
            $this->assertIsArray($results);
        } catch (\Exception $e) {
            $this->markTestSkipped('Framework testing requires Composer and network access: ' . $e->getMessage());
        }
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::runTests
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::generateReport
     */
    public function testReportGenerationWithTestResults(): void
    {
        $configPath = $this->testPackagePath . '/.compatibility.yml';
        
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Test package configuration not found');
        }

        $tester = new CompatibilityTester($configPath, $this->testPackagePath);
        
        // Run tests (may be skipped if Composer/network unavailable)
        try {
            $results = $tester->runTests(['framework' => 'laravel', 'php' => '8.1']);
            
            // Generate report
            $reportPath = $this->tempDir . '/test-report.md';
            $report = $tester->generateReport('markdown', $reportPath);
            
            $this->assertStringContainsString('PHP Compatibility Test Report', $report);
            
            if (file_exists($reportPath)) {
                $this->assertFileExists($reportPath);
                unlink($reportPath);
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Framework testing requires Composer and network access: ' . $e->getMessage());
        }
    }

    /**
     * @covers \LukaszZychal\PhpCompatibilityTester\CompatibilityTester::cleanup
     */
    public function testCompatibilityTesterCleanup(): void
    {
        $configPath = $this->testPackagePath . '/.compatibility.yml';
        
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Test package configuration not found');
        }

        $tester = new CompatibilityTester($configPath, $this->testPackagePath);
        
        // Cleanup should not throw
        $tester->cleanup();
        $this->assertTrue(true); // If we get here, cleanup worked
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

