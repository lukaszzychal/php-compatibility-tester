<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester;

use LukaszZychal\PhpCompatibilityTester\Exception\CompatibilityException;
use Symfony\Component\Process\Process;

/**
 * Tests Composer dependency resolution for different PHP versions.
 *
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
class ComposerTester
{
    private string $packageName;
    private string $packagePath;

    public function __construct(string $packageName, string $packagePath)
    {
        $this->packageName = $packageName;
        $this->packagePath = $packagePath;
    }

    /**
     * Test if the package can be installed with a specific PHP version.
     *
     * @param string $phpVersion PHP version to test (e.g., '8.1', '8.2')
     * @param string $workingDir Working directory for the test
     * @return array Test result with 'success', 'error', and 'output' keys
     */
    public function testPhpVersion(string $phpVersion, string $workingDir): array
    {
        $result = [
            'success' => false,
            'error' => null,
            'output' => '',
            'php_version' => $phpVersion,
        ];

        try {
            // Create a temporary composer.json to test dependency resolution
            $composerJson = [
                'require' => [
                    'php' => "^$phpVersion",
                    $this->packageName => '*',
                ],
            ];

            $composerJsonPath = $workingDir . '/composer.json';
            file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Try to resolve dependencies
            $process = new Process(['composer', 'update', '--no-install', '--dry-run'], $workingDir);
            $process->setTimeout(300);
            $process->run();

            $result['output'] = $process->getOutput() . $process->getErrorOutput();
            $result['success'] = $process->isSuccessful();

            if (!$result['success']) {
                $result['error'] = "Composer dependency resolution failed for PHP {$phpVersion}";
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = $e->getTraceAsString();
        }

        return $result;
    }

    /**
     * Test if the package can be installed in a framework project.
     *
     * @param string $framework Framework name
     * @param string $frameworkVersion Framework version
     * @param string $phpVersion PHP version
     * @param string $workingDir Working directory (framework project)
     * @return array Test result
     */
    public function testFrameworkInstallation(
        string $framework,
        string $frameworkVersion,
        string $phpVersion,
        string $workingDir
    ): array {
        $result = [
            'success' => false,
            'error' => null,
            'output' => '',
            'framework' => $framework,
            'framework_version' => $frameworkVersion,
            'php_version' => $phpVersion,
        ];

        try {
            // Try to require the package in the framework project
            $process = new Process(
                ['composer', 'require', $this->packageName, '--no-update'],
                $workingDir
            );
            $process->setTimeout(300);
            $process->run();

            $output = $process->getOutput() . $process->getErrorOutput();
            $result['output'] = $output;

            if (!$process->isSuccessful()) {
                $result['error'] = "Failed to add package to {$framework} {$frameworkVersion}";
                return $result;
            }

            // Try to update/install
            $process = new Process(['composer', 'update', '--no-interaction'], $workingDir);
            $process->setTimeout(600);
            $process->run();

            $result['output'] .= "\n" . $process->getOutput() . $process->getErrorOutput();
            $result['success'] = $process->isSuccessful();

            if (!$result['success']) {
                $result['error'] = "Failed to install package in {$framework} {$frameworkVersion}";
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = $e->getTraceAsString();
        }

        return $result;
    }
}

