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

/**
 * Command to generate compatibility test reports.
 */
class ReportCommand extends Command
{
    protected static $defaultName = 'report';
    protected static $defaultDescription = 'Generate compatibility test report';

    protected function configure(): void
    {
        $this
            ->setName('report')
            ->setDescription('Generate compatibility test report')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Report format (markdown, json, html)', 'markdown')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file path')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to configuration file', '.compatibility.yml')
            ->addOption('package-path', null, InputOption::VALUE_REQUIRED, 'Path to the package being tested');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('PHP Compatibility Tester - Generate Report');

        $configPath = $input->getOption('config');
        $format = $input->getOption('format');
        $outputPath = $input->getOption('output');
        $packagePath = $input->getOption('package-path') ?? getcwd();

        if (!in_array($format, ['markdown', 'json', 'html'])) {
            $io->error("Invalid format: {$format}. Supported formats: markdown, json, html");
            return Command::FAILURE;
        }

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

        // Run tests first if no results exist
        $io->section('Running tests...');
        $tester->runTests();

        // Generate report
        $io->section('Generating report...');
        
        try {
            $reportContent = $tester->generateReport($format, $outputPath);
            
            if ($outputPath) {
                $io->success("Report generated: {$outputPath}");
            } else {
                $io->section('Report:');
                $io->writeln($reportContent);
            }
        } catch (\Exception $e) {
            $io->error('Failed to generate report: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Cleanup
        $tester->cleanup();

        return Command::SUCCESS;
    }
}

