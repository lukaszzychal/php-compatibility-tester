<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester;

use LukaszZychal\PhpCompatibilityTester\Exception\CompatibilityException;
use Symfony\Component\Process\Process;

/**
 * Tests compatibility with various PHP frameworks.
 */
class FrameworkTester
{
    private string $packageName;
    private string $packagePath;
    private string $tempDir;

    public function __construct(string $packageName, string $packagePath, ?string $tempDir = null)
    {
        $this->packageName = $packageName;
        $this->packagePath = $packagePath;
        $this->tempDir = $tempDir ?? sys_get_temp_dir() . '/php-compatibility-tester';
    }

    /**
     * Test compatibility with a specific framework version.
     *
     * @param string $framework Framework name (e.g., 'laravel', 'symfony')
     * @param string $version Framework version (e.g., '11.*', '7.4.*')
     * @param string $phpVersion PHP version to use
     * @param array $frameworkConfig Framework configuration from config file
     * @param array $testScripts Test scripts to run
     * @return array Test result
     */
    public function testFramework(
        string $framework,
        string $version,
        string $phpVersion,
        array $frameworkConfig,
        array $testScripts = []
    ): array {
        $result = [
            'success' => false,
            'error' => null,
            'output' => '',
            'framework' => $framework,
            'framework_version' => $version,
            'php_version' => $phpVersion,
            'test_results' => [],
        ];

        $projectDir = $this->tempDir . '/' . $framework . '-' . str_replace(['.', '*'], ['-', 'x'], $version) . '-' . $phpVersion;

        try {
            // Check PHP version requirement
            $minPhpVersion = $this->getMinPhpVersion($frameworkConfig, $version, $phpVersion);
            if ($minPhpVersion && version_compare($phpVersion, $minPhpVersion, '<')) {
                $result['error'] = "PHP {$phpVersion} does not meet minimum requirement {$minPhpVersion} for {$framework} {$version}";
                $result['skipped'] = true;
                return $result;
            }

            // Create framework project
            if (!is_dir($projectDir)) {
                $this->createFrameworkProject($framework, $version, $frameworkConfig, $projectDir);
            }

            if (!is_dir($projectDir)) {
                $result['error'] = "Failed to create framework project";
                return $result;
            }

            // Install the package
            $installResult = $this->installPackage($projectDir);
            if (!$installResult['success']) {
                $result['error'] = $installResult['error'];
                $result['output'] = $installResult['output'];
                return $result;
            }

            // Run test scripts
            foreach ($testScripts as $testScript) {
                $testResult = $this->runTestScript($projectDir, $testScript);
                $result['test_results'][$testScript['name']] = $testResult;
            }

            $result['success'] = true;
            $result['output'] = "Framework project created and package installed successfully";

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = $e->getTraceAsString();
        } finally {
            // Cleanup can be done here if needed
            // For now, we keep the projects for inspection
        }

        return $result;
    }

    /**
     * Get minimum PHP version for a framework version.
     */
    private function getMinPhpVersion(array $frameworkConfig, string $version, string $phpVersion): ?string
    {
        // Check for version-specific PHP requirement (e.g., php_min_version_12 for Laravel 12)
        $versionKey = str_replace(['.', '*'], ['_', ''], $version);
        $versionSpecificKey = 'php_min_version_' . $versionKey;

        if (isset($frameworkConfig[$versionSpecificKey])) {
            return $frameworkConfig[$versionSpecificKey];
        }

        // Fall back to general php_min_version
        return $frameworkConfig['php_min_version'] ?? null;
    }

    /**
     * Create a framework project.
     */
    private function createFrameworkProject(
        string $framework,
        string $version,
        array $frameworkConfig,
        string $projectDir
    ): void {
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $installCommand = $frameworkConfig['install_command'];
        
        // Replace version placeholder if present
        $installCommand = str_replace('{version}', $version, $installCommand);
        
        // Parse command into parts
        $commandParts = explode(' ', $installCommand);
        $command = array_shift($commandParts);
        $commandParts[] = $projectDir;

        $process = new Process(array_merge([$command], $commandParts));
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new CompatibilityException(
                "Failed to create framework project: " . $process->getErrorOutput()
            );
        }
    }

    /**
     * Install the package in the framework project.
     */
    private function installPackage(string $projectDir): array
    {
        $result = ['success' => false, 'error' => null, 'output' => ''];

        try {
            // Add the package as a local repository
            $composerJsonPath = $projectDir . '/composer.json';
            if (!file_exists($composerJsonPath)) {
                $result['error'] = "composer.json not found in framework project";
                return $result;
            }

            $composerJson = json_decode(file_get_contents($composerJsonPath), true);
            
            // Add repository configuration
            if (!isset($composerJson['repositories'])) {
                $composerJson['repositories'] = [];
            }

            $composerJson['repositories'][] = [
                'type' => 'path',
                'url' => $this->packagePath,
            ];

            file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Require the package
            $process = new Process(['composer', 'require', $this->packageName . ':dev-master', '--no-interaction'], $projectDir);
            $process->setTimeout(600);
            $process->run();

            $result['output'] = $process->getOutput() . $process->getErrorOutput();
            $result['success'] = $process->isSuccessful();

            if (!$result['success']) {
                $result['error'] = "Failed to install package: " . $process->getErrorOutput();
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = $e->getTraceAsString();
        }

        return $result;
    }

    /**
     * Run a test script in the framework project.
     */
    private function runTestScript(string $projectDir, array $testScript): array
    {
        $result = [
            'success' => false,
            'error' => null,
            'output' => '',
            'script' => $testScript['name'],
        ];

        $scriptPath = $testScript['script'];
        
        // If script path is relative, make it relative to the project directory
        if (!file_exists($scriptPath)) {
            $scriptPath = $projectDir . '/' . $scriptPath;
        }

        if (!file_exists($scriptPath)) {
            $result['error'] = "Test script not found: {$testScript['script']}";
            return $result;
        }

        try {
            $process = new Process(['php', $scriptPath], $projectDir);
            $process->setTimeout(300);
            $process->run();

            $result['output'] = $process->getOutput() . $process->getErrorOutput();
            $result['success'] = $process->isSuccessful();

            if (!$result['success']) {
                $result['error'] = "Test script failed with exit code: " . $process->getExitCode();
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = $e->getTraceAsString();
        }

        return $result;
    }

    /**
     * Clean up temporary directories.
     */
    public function cleanup(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }

    /**
     * Recursively remove a directory.
     */
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

