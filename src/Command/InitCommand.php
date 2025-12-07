<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to initialize compatibility testing in a project.
 */
class InitCommand extends Command
{
    protected static $defaultName = 'init';
    protected static $defaultDescription = 'Initialize compatibility testing configuration';

    private string $packagePath;

    public function __construct(string $packagePath = null)
    {
        parent::__construct();
        $this->packagePath = $packagePath ?? dirname(__DIR__, 2);
    }

    protected function configure(): void
    {
        $this->setDescription('Initialize compatibility testing configuration and templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('PHP Compatibility Tester - Initialization');

        $currentDir = getcwd();
        $configPath = $currentDir . '/.compatibility.yml';

        // Check if config already exists
        if (file_exists($configPath)) {
            if (!$io->confirm('Configuration file already exists. Overwrite?', false)) {
                $io->warning('Initialization cancelled.');
                return Command::FAILURE;
            }
        }

        // Copy example configuration
        $exampleConfig = $this->packagePath . '/templates/config/.compatibility.yml.example';
        if (file_exists($exampleConfig)) {
            copy($exampleConfig, $configPath);
            $io->success('Created .compatibility.yml');
        } else {
            // Create a basic config if template doesn't exist
            $this->createBasicConfig($configPath);
            $io->success('Created .compatibility.yml');
        }

        // Copy PHPUnit test templates
        $phpunitDir = $currentDir . '/tests/compatibility';
        if (!is_dir($phpunitDir)) {
            mkdir($phpunitDir, 0755, true);
        }

        $frameworkTestTemplate = $this->packagePath . '/templates/phpunit/FrameworkCompatibilityTest.php';
        $composerTestTemplate = $this->packagePath . '/templates/phpunit/ComposerCompatibilityTest.php';

        if (file_exists($frameworkTestTemplate)) {
            copy($frameworkTestTemplate, $phpunitDir . '/FrameworkCompatibilityTest.php');
            $io->success('Copied FrameworkCompatibilityTest.php template');
        }

        if (file_exists($composerTestTemplate)) {
            copy($composerTestTemplate, $phpunitDir . '/ComposerCompatibilityTest.php');
            $io->success('Copied ComposerCompatibilityTest.php template');
        }

        // Copy GitHub Actions workflow
        $githubDir = $currentDir . '/.github/workflows';
        if (!is_dir($githubDir)) {
            mkdir($githubDir, 0755, true);
        }

        $workflowTemplate = $this->packagePath . '/templates/github-actions/compatibility-tests.yml';
        if (file_exists($workflowTemplate)) {
            copy($workflowTemplate, $githubDir . '/compatibility-tests.yml');
            $io->success('Copied GitHub Actions workflow');
        }

        // Copy test script template
        $scriptsDir = $currentDir . '/scripts';
        if (!is_dir($scriptsDir)) {
            mkdir($scriptsDir, 0755, true);
        }

        $scriptTemplate = $this->packagePath . '/templates/scripts/compatibility-test.sh';
        if (file_exists($scriptTemplate)) {
            copy($scriptTemplate, $scriptsDir . '/compatibility-test.sh');
            chmod($scriptsDir . '/compatibility-test.sh', 0755);
            $io->success('Copied compatibility test script');
        }

        $io->newLine();
        $io->success('Initialization complete!');
        $io->note('Please edit .compatibility.yml to configure your compatibility tests.');

        return Command::SUCCESS;
    }

    /**
     * Create a basic configuration file.
     */
    private function createBasicConfig(string $configPath): void
    {
        $composerJsonPath = getcwd() . '/composer.json';
        $packageName = 'vendor/package-name';
        
        if (file_exists($composerJsonPath)) {
            $composerJson = json_decode(file_get_contents($composerJsonPath), true);
            if (isset($composerJson['name'])) {
                $packageName = $composerJson['name'];
            }
        }

        $config = [
            'package_name' => $packageName,
            'php_versions' => ['8.1', '8.2', '8.3', '8.4'],
            'frameworks' => [
                'laravel' => [
                    'versions' => ['11.*'],
                    'install_command' => 'composer create-project laravel/laravel',
                    'php_min_version' => '8.1',
                ],
            ],
            'test_scripts' => [],
        ];

        $yaml = $this->arrayToYaml($config);
        file_put_contents($configPath, $yaml);
    }

    /**
     * Simple array to YAML converter (basic implementation).
     */
    private function arrayToYaml(array $data, int $indent = 0): string
    {
        $yaml = '';
        $spaces = str_repeat('  ', $indent);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->isAssociative($value)) {
                    $yaml .= $spaces . $key . ":\n";
                    $yaml .= $this->arrayToYaml($value, $indent + 1);
                } else {
                    $yaml .= $spaces . $key . ":\n";
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            $yaml .= $spaces . "  -\n";
                            $yaml .= $this->arrayToYaml($item, $indent + 2);
                        } else {
                            $yaml .= $spaces . "  - " . $this->formatValue($item) . "\n";
                        }
                    }
                }
            } else {
                $yaml .= $spaces . $key . ': ' . $this->formatValue($value) . "\n";
            }
        }

        return $yaml;
    }

    /**
     * Check if array is associative.
     */
    private function isAssociative(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Format a value for YAML output.
     */
    private function formatValue($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_string($value) && (strpos($value, ' ') !== false || strpos($value, ':') !== false)) {
            return '"' . addcslashes($value, '"') . '"';
        }
        return (string) $value;
    }
}

