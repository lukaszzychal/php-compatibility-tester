<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester;

use LukaszZychal\PhpCompatibilityTester\Exception\ConfigurationException;

/**
 * Main orchestrator class for compatibility testing.
 *
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
class CompatibilityTester
{
    private ConfigLoader $configLoader;
    private FrameworkTester $frameworkTester;
    private ComposerTester $composerTester;
    private ReportGenerator $reportGenerator;
    private array $config = [];
    private string $packagePath;

    public function __construct(string $configPath, ?string $packagePath = null)
    {
        $this->configLoader = new ConfigLoader();
        $this->packagePath = $packagePath ?? getcwd();
        
        try {
            $this->config = $this->configLoader->load($configPath);
        } catch (ConfigurationException $e) {
            throw $e;
        }

        $packageName = $this->config['package_name'] ?? 'unknown/package';
        $this->frameworkTester = new FrameworkTester($packageName, $this->packagePath);
        $this->composerTester = new ComposerTester($packageName, $this->packagePath);
        $this->reportGenerator = new ReportGenerator();
    }

    /**
     * Run all compatibility tests.
     *
     * @param array $filters Optional filters: 'framework', 'version', 'php'
     * @return array Test results
     */
    public function runTests(array $filters = []): array
    {
        $results = [];
        $phpVersions = $this->config['php_versions'] ?? [];
        $frameworks = $this->config['frameworks'] ?? [];
        $testScripts = $this->config['test_scripts'] ?? [];

        // Filter PHP versions if specified
        if (isset($filters['php'])) {
            $phpVersions = array_filter($phpVersions, fn($v) => $this->matchesVersion($v, $filters['php']));
        }

        // Test Composer dependency resolution for each PHP version
        foreach ($phpVersions as $phpVersion) {
            if (empty($filters['framework'])) {
                $tempDir = sys_get_temp_dir() . '/php-compatibility-tester-composer-' . $phpVersion;
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $result = $this->composerTester->testPhpVersion($phpVersion, $tempDir);
                $results[] = $result;
            }
        }

        // Test framework compatibility
        foreach ($frameworks as $framework => $frameworkConfig) {
            // Filter by framework if specified
            if (isset($filters['framework']) && $framework !== $filters['framework']) {
                continue;
            }

            $versions = $frameworkConfig['versions'] ?? [];
            
            // Filter by version if specified
            if (isset($filters['version'])) {
                $versions = array_filter($versions, fn($v) => $this->matchesVersion($v, $filters['version']));
            }

            foreach ($versions as $version) {
                foreach ($phpVersions as $phpVersion) {
                    $result = $this->frameworkTester->testFramework(
                        $framework,
                        $version,
                        $phpVersion,
                        $frameworkConfig,
                        $testScripts
                    );
                    $results[] = $result;
                }
            }
        }

        $this->reportGenerator->setResults($results);
        return $results;
    }

    /**
     * Generate a report from test results.
     *
     * @param string $format Report format (markdown, json, html)
     * @param string|null $outputPath Optional path to save the report
     * @return string Generated report content
     */
    public function generateReport(string $format = 'markdown', ?string $outputPath = null): string
    {
        return $this->reportGenerator->generate($format, $outputPath);
    }

    /**
     * Get the loaded configuration.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Check if a version matches a pattern.
     *
     * @param string $version Version to check
     * @param string $pattern Pattern to match (supports wildcards)
     * @return bool
     */
    private function matchesVersion(string $version, string $pattern): bool
    {
        // Convert wildcard pattern to regex
        $pattern = str_replace(['.', '*'], ['\.', '.*'], $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        return (bool) preg_match($pattern, $version);
    }

    /**
     * Clean up temporary files and directories.
     */
    public function cleanup(): void
    {
        $this->frameworkTester->cleanup();
    }
}

