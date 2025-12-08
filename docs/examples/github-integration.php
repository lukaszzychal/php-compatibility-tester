<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester\Examples;

use LukaszZychal\PhpCompatibilityTester\ReportGenerator;

/**
 * Przykład integracji z GitHub MCP Server
 * 
 * Ta klasa pokazuje jak można wykorzystać GitHub MCP Server
 * do automatycznego raportowania wyników testów kompatybilności.
 * 
 * Uwaga: Rzeczywiste wywołania GitHub API są wykonywane przez asystenta AI
 * poprzez MCP Server, nie bezpośrednio z tego kodu.
 *
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
class GitHubIntegrationExample
{
    private ReportGenerator $reportGenerator;

    public function __construct(ReportGenerator $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }

    /**
     * Przykład 1: Komentarz w PR z wynikami testów
     * 
     * Asystent AI może wywołać: add_issue_comment
     */
    public function commentOnPullRequest(array $results, int $prNumber): string
    {
        $report = $this->reportGenerator->generate('markdown');
        
        $comment = "## 🔍 Compatibility Test Results\n\n";
        $comment .= $this->formatSummary($results);
        $comment .= "\n\n<details>\n<summary>📋 Detailed Report</summary>\n\n";
        $comment .= $report;
        $comment .= "\n</details>\n\n";
        $comment .= "*Generated automatically by PHP Compatibility Tester*";
        
        // Asystent AI wywoła:
        // add_issue_comment({
        //   owner: "lukaszzychal",
        //   repo: "php-compatibility-tester",
        //   issue_number: $prNumber,
        //   body: $comment
        // })
        
        return $comment;
    }

    /**
     * Przykład 2: Tworzenie issue z błędami kompatybilności
     * 
     * Asystent AI może wywołać: create_issue
     */
    public function createCompatibilityIssue(array $failedTests): string
    {
        if (empty($failedTests)) {
            return '';
        }

        $title = "🚨 Compatibility Issues Detected";
        $body = "## Compatibility Test Failures\n\n";
        $body .= "The following compatibility tests have failed:\n\n";

        foreach ($failedTests as $test) {
            $framework = $test['framework'] ?? 'Unknown';
            $version = $test['framework_version'] ?? 'Unknown';
            $php = $test['php_version'] ?? 'Unknown';
            $error = $test['error'] ?? 'Unknown error';

            $body .= sprintf(
                "### ❌ %s %s (PHP %s)\n\n",
                $framework,
                $version,
                $php
            );
            $body .= "**Error:** `{$error}`\n\n";
            
            if (isset($test['test_results'])) {
                $body .= "**Failed Tests:**\n";
                foreach ($test['test_results'] as $testName => $testResult) {
                    if (!($testResult['success'] ?? false)) {
                        $body .= "- {$testName}\n";
                    }
                }
                $body .= "\n";
            }
            
            $body .= "---\n\n";
        }

        $body .= "\n*This issue was created automatically by PHP Compatibility Tester.*\n";
        $body .= "*Please review and fix the compatibility issues.*";

        // Asystent AI wywoła:
        // create_issue({
        //   owner: "lukaszzychal",
        //   repo: "php-compatibility-tester",
        //   title: $title,
        //   body: $body,
        //   labels: ["compatibility", "bug", "automated"]
        // })

        return $body;
    }

    /**
     * Przykład 3: Aktualizacja badge'ów w README
     * 
     * Asystent AI może wywołać: update_file
     */
    public function updateReadmeBadges(array $results): array
    {
        $total = count($results);
        $successful = count(array_filter($results, fn($r) => $r['success'] ?? false));
        $failed = $total - $successful;
        $successRate = $total > 0 ? round(($successful / $total) * 100, 2) : 0;

        $badges = [
            'compatibility' => sprintf(
                '![Compatibility](https://img.shields.io/badge/Compatibility-%s%%25-%s)',
                $successRate,
                $this->getBadgeColor($successRate)
            ),
            'tests' => sprintf(
                '![Tests](https://img.shields.io/badge/Tests-%d%%20passed-%s)',
                $successful,
                $this->getBadgeColor($successRate)
            ),
        ];

        // Asystent AI może:
        // 1. Pobrać README: get_file_content
        // 2. Zaktualizować badge'e
        // 3. Zaktualizować plik: update_file

        return $badges;
    }

    /**
     * Przykład 4: Cotygodniowy raport kompatybilności
     * 
     * Asystent AI może wywołać: create_issue
     */
    public function generateWeeklyReport(array $results): string
    {
        $report = "# 📊 Weekly Compatibility Report\n\n";
        $report .= "**Date:** " . date('Y-m-d') . "\n\n";
        
        $report .= $this->formatSummary($results);
        $report .= "\n\n";
        
        // Statystyki per framework
        $report .= "## Framework Statistics\n\n";
        $frameworkStats = $this->calculateFrameworkStats($results);
        foreach ($frameworkStats as $framework => $stats) {
            $report .= sprintf(
                "### %s\n- Total: %d\n- Passed: %d\n- Failed: %d\n- Success Rate: %.2f%%\n\n",
                $framework,
                $stats['total'],
                $stats['passed'],
                $stats['failed'],
                $stats['success_rate']
            );
        }

        // Trendy (wymagałoby przechowywania historycznych danych)
        $report .= "\n## Recommendations\n\n";
        $report .= $this->generateRecommendations($results);

        // Asystent AI wywoła:
        // create_issue({
        //   owner: "lukaszzychal",
        //   repo: "php-compatibility-tester",
        //   title: "Weekly Compatibility Report - " . date('Y-m-d'),
        //   body: $report,
        //   labels: ["report", "automated", "weekly"]
        // })

        return $report;
    }

    /**
     * Przykład 5: Automatyczne tagowanie osób odpowiedzialnych
     * 
     * Asystent AI może wywołać: add_issue_comment z @mentions
     */
    public function notifyMaintainers(array $failedTests): string
    {
        $comment = "## ⚠️ Compatibility Issues Require Attention\n\n";
        
        // Grupuj błędy per framework
        $byFramework = [];
        foreach ($failedTests as $test) {
            $framework = $test['framework'] ?? 'unknown';
            $byFramework[$framework][] = $test;
        }

        foreach ($byFramework as $framework => $tests) {
            $comment .= "### {$framework}\n\n";
            $comment .= sprintf("Found %d compatibility issue(s)\n\n", count($tests));
            
            // Taguj odpowiednie osoby (przykład)
            $maintainers = $this->getFrameworkMaintainers($framework);
            if (!empty($maintainers)) {
                $comment .= "CC: " . implode(' ', array_map(fn($m) => "@{$m}", $maintainers)) . "\n\n";
            }
        }

        // Asystent AI wywoła:
        // add_issue_comment({
        //   owner: "lukaszzychal",
        //   repo: "php-compatibility-tester",
        //   issue_number: $issueNumber,
        //   body: $comment
        // })

        return $comment;
    }

    /**
     * Przykład 6: Sprawdzanie statusu workflow
     * 
     * Asystent AI może wywołać: list_workflow_runs, get_workflow_run
     */
    public function checkWorkflowStatus(string $workflowName = 'ci.yml'): array
    {
        // Asystent AI może:
        // 1. Pobrać listę workflow runs: list_workflow_runs
        // 2. Sprawdzić status ostatniego: get_workflow_run
        // 3. Pobrać logi jeśli failed: get_workflow_run_logs

        return [
            'workflow' => $workflowName,
            'status' => 'unknown',
            'conclusion' => 'unknown',
            'runs' => [],
        ];
    }

    /**
     * Przykład 7: Wyszukiwanie podobnych issues
     * 
     * Asystent AI może wywołać: search_issues
     */
    public function findSimilarIssues(string $errorMessage): array
    {
        // Asystent AI może:
        // search_issues({
        //   query: "label:compatibility " . urlencode($errorMessage),
        //   owner: "lukaszzychal",
        //   repo: "php-compatibility-tester"
        // })

        return [];
    }

    // Helper methods

    private function formatSummary(array $results): string
    {
        $total = count($results);
        $successful = count(array_filter($results, fn($r) => $r['success'] ?? false));
        $failed = $total - $successful;
        $successRate = $total > 0 ? round(($successful / $total) * 100, 2) : 0;

        $summary = "## Summary\n\n";
        $summary .= sprintf("- **Total Tests:** %d\n", $total);
        $summary .= sprintf("- **✅ Passed:** %d\n", $successful);
        $summary .= sprintf("- **❌ Failed:** %d\n", $failed);
        $summary .= sprintf("- **Success Rate:** %.2f%%\n", $successRate);

        return $summary;
    }

    private function calculateFrameworkStats(array $results): array
    {
        $stats = [];

        foreach ($results as $result) {
            $framework = $result['framework'] ?? 'unknown';
            
            if (!isset($stats[$framework])) {
                $stats[$framework] = [
                    'total' => 0,
                    'passed' => 0,
                    'failed' => 0,
                ];
            }

            $stats[$framework]['total']++;
            if ($result['success'] ?? false) {
                $stats[$framework]['passed']++;
            } else {
                $stats[$framework]['failed']++;
            }
        }

        foreach ($stats as &$stat) {
            $stat['success_rate'] = $stat['total'] > 0 
                ? round(($stat['passed'] / $stat['total']) * 100, 2) 
                : 0;
        }

        return $stats;
    }

    private function generateRecommendations(array $results): string
    {
        $recommendations = [];
        
        $failedTests = array_filter($results, fn($r) => !($r['success'] ?? false));
        
        if (empty($failedTests)) {
            return "✅ All compatibility tests passed! No action needed.\n";
        }

        // Analizuj typy błędów
        $errorTypes = [];
        foreach ($failedTests as $test) {
            $error = $test['error'] ?? 'Unknown';
            $errorType = $this->categorizeError($error);
            $errorTypes[$errorType] = ($errorTypes[$errorType] ?? 0) + 1;
        }

        if (isset($errorTypes['dependency'])) {
            $recommendations[] = "🔧 **Dependency Issues:** Consider updating `composer.json` requirements";
        }
        
        if (isset($errorTypes['php_version'])) {
            $recommendations[] = "🐘 **PHP Version:** Some frameworks require newer PHP versions";
        }
        
        if (isset($errorTypes['autoload'])) {
            $recommendations[] = "📦 **Autoloading:** Check PSR-4 autoload configuration";
        }

        return implode("\n", $recommendations) . "\n";
    }

    private function categorizeError(string $error): string
    {
        $error = strtolower($error);
        
        if (str_contains($error, 'dependency') || str_contains($error, 'require')) {
            return 'dependency';
        }
        
        if (str_contains($error, 'php') && str_contains($error, 'version')) {
            return 'php_version';
        }
        
        if (str_contains($error, 'autoload') || str_contains($error, 'class not found')) {
            return 'autoload';
        }
        
        return 'other';
    }

    private function getBadgeColor(float $successRate): string
    {
        if ($successRate >= 90) {
            return 'brightgreen';
        } elseif ($successRate >= 70) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    private function getFrameworkMaintainers(string $framework): array
    {
        // Przykładowa mapa maintainerów
        $maintainers = [
            'laravel' => ['lukaszzychal'],
            'symfony' => ['lukaszzychal'],
            'codeigniter' => ['lukaszzychal'],
        ];

        return $maintainers[strtolower($framework)] ?? [];
    }
}

