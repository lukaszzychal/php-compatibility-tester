<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester\Command;

use LukaszZychal\PhpCompatibilityTester\CompatibilityTester;
use LukaszZychal\PhpCompatibilityTester\Exception\ConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Command to run compatibility tests.
 */
class TestCommand extends Command
{
    protected static $defaultName = 'test';
    protected static $defaultDescription = 'Run compatibility tests';

    protected function configure(): void
    {
        $this
            ->setDescription('Run compatibility tests')
            ->addOption('framework', 'f', InputOption::VALUE_REQUIRED, 'Filter by framework name')
            ->addOption('version', null, InputOption::VALUE_REQUIRED, 'Filter by framework version')
            ->addOption('php', 'p', InputOption::VALUE_REQUIRED, 'Filter by PHP version')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to configuration file', '.compatibility.yml')
            ->addOption('package-path', null, InputOption::VALUE_REQUIRED, 'Path to the package being tested');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('PHP Compatibility Tester - Running Tests');

        $configPath = $input->getOption('config');
        $packagePath = $input->getOption('package-path') ?? getcwd();

        if (!file_exists($configPath)) {
            $io->error("Configuration file not found: {$configPath}");
            $io->note('Run "compatibility-tester init" to create a configuration file.');
            return Command::FAILURE;
        }

        try {
            $tester = new CompatibilityTester($configPath, $packagePath);
        } catch (ConfigurationException $e) {
            $io->error('Configuration error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $filters = [];
        if ($input->getOption('framework')) {
            $filters['framework'] = $input->getOption('framework');
        }
        if ($input->getOption('version')) {
            $filters['version'] = $input->getOption('version');
        }
        if ($input->getOption('php')) {
            $filters['php'] = $input->getOption('php');
        }

        if (!empty($filters)) {
            $io->section('Filters applied:');
            foreach ($filters as $key => $value) {
                $io->text("  - {$key}: {$value}");
            }
        }

        $io->section('Running tests...');
        $io->newLine();

        // Run tests
        $results = $tester->runTests($filters);

        // Display results
        $io->newLine();
        $io->section('Test Results');

        $total = count($results);
        $successful = count(array_filter($results, fn($r) => ($r['success'] ?? false) === true));
        $failed = $total - $successful;

        if ($total === 0) {
            $io->warning('No tests were run. Check your filters and configuration.');
            return Command::FAILURE;
        }

        $io->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $total],
                ['Successful', $successful],
                ['Failed', $failed],
                ['Success Rate', $total > 0 ? round(($successful / $total) * 100, 2) . '%' : '0%'],
            ]
        );

        // Show detailed results
        $io->newLine();
        foreach ($results as $result) {
            $status = ($result['success'] ?? false) ? '✅' : '❌';
            $title = '';

            if (isset($result['framework'])) {
                $title = "{$status} {$result['framework']} {$result['framework_version']} (PHP {$result['php_version']})";
            } elseif (isset($result['php_version'])) {
                $title = "{$status} PHP {$result['php_version']}";
            } else {
                $title = "{$status} Test";
            }

            if ($result['success'] ?? false) {
                $io->success($title);
            } else {
                $io->error($title);
                if (isset($result['error']) && $result['error']) {
                    $io->text('  Error: ' . $result['error']);
                }
            }
        }

        // Cleanup
        $tester->cleanup();

        return $failed === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}

