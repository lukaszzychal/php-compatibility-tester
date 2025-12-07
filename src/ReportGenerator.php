<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester;

use LukaszZychal\PhpCompatibilityTester\Exception\CompatibilityException;

/**
 * Generates compatibility test reports in various formats.
 */
class ReportGenerator
{
    private array $results = [];

    /**
     * Add test results.
     *
     * @param array $results Test results to add
     */
    public function addResults(array $results): void
    {
        $this->results = array_merge($this->results, $results);
    }

    /**
     * Set test results.
     *
     * @param array $results Test results
     */
    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    /**
     * Generate a report in the specified format.
     *
     * @param string $format Report format (markdown, json, html)
     * @param string|null $outputPath Optional path to save the report
     * @return string Generated report content
     * @throws CompatibilityException
     */
    public function generate(string $format = 'markdown', ?string $outputPath = null): string
    {
        $content = match ($format) {
            'markdown' => $this->generateMarkdown(),
            'json' => $this->generateJson(),
            'html' => $this->generateHtml(),
            default => throw new CompatibilityException("Unsupported report format: {$format}"),
        };

        if ($outputPath !== null) {
            $dir = dirname($outputPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($outputPath, $content);
        }

        return $content;
    }

    /**
     * Generate Markdown report.
     */
    private function generateMarkdown(): string
    {
        $report = "# PHP Compatibility Test Report\n\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

        if (empty($this->results)) {
            $report .= "No test results available.\n";
            return $report;
        }

        // Summary
        $total = count($this->results);
        $successful = count(array_filter($this->results, fn($r) => ($r['success'] ?? false) === true));
        $failed = $total - $successful;

        $report .= "## Summary\n\n";
        $report .= "- **Total Tests**: {$total}\n";
        $report .= "- **Successful**: {$successful}\n";
        $report .= "- **Failed**: {$failed}\n";
        $report .= "- **Success Rate**: " . ($total > 0 ? round(($successful / $total) * 100, 2) : 0) . "%\n\n";

        // Detailed results
        $report .= "## Test Results\n\n";

        foreach ($this->results as $result) {
            $status = ($result['success'] ?? false) ? '✅ PASS' : '❌ FAIL';
            
            if (isset($result['framework'])) {
                $report .= "### {$result['framework']} {$result['framework_version']} (PHP {$result['php_version']}) {$status}\n\n";
            } elseif (isset($result['php_version'])) {
                $report .= "### PHP {$result['php_version']} {$status}\n\n";
            } else {
                $report .= "### Test {$status}\n\n";
            }

            if (isset($result['error']) && $result['error']) {
                $report .= "**Error**: {$result['error']}\n\n";
            }

            if (isset($result['test_results']) && is_array($result['test_results'])) {
                $report .= "#### Test Scripts\n\n";
                foreach ($result['test_results'] as $testName => $testResult) {
                    $testStatus = ($testResult['success'] ?? false) ? '✅' : '❌';
                    $report .= "- {$testStatus} **{$testName}**\n";
                    if (isset($testResult['error']) && $testResult['error']) {
                        $report .= "  - Error: {$testResult['error']}\n";
                    }
                }
                $report .= "\n";
            }

            if (isset($result['output']) && $result['output']) {
                $report .= "#### Output\n\n";
                $report .= "```\n" . $result['output'] . "\n```\n\n";
            }

            $report .= "---\n\n";
        }

        return $report;
    }

    /**
     * Generate JSON report.
     */
    private function generateJson(): string
    {
        $data = [
            'generated_at' => date('c'),
            'summary' => $this->generateSummary(),
            'results' => $this->results,
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate HTML report.
     */
    private function generateHtml(): string
    {
        $total = count($this->results);
        $successful = count(array_filter($this->results, fn($r) => ($r['success'] ?? false) === true));
        $failed = $total - $successful;
        $successRate = $total > 0 ? round(($successful / $total) * 100, 2) : 0;

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Compatibility Test Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 30px; }
        h3 { color: #666; margin-top: 20px; }
        .summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            font-size: 18px;
        }
        .summary-item strong {
            color: #333;
        }
        .result {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .result.pass { border-left: 4px solid #28a745; }
        .result.fail { border-left: 4px solid #dc3545; }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .status.pass { background: #d4edda; color: #155724; }
        .status.fail { background: #f8d7da; color: #721c24; }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .output {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .test-script {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>PHP Compatibility Test Report</h1>
    <p>Generated: {$this->formatDate()}</p>
    
    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-item"><strong>Total Tests:</strong> {$total}</div>
        <div class="summary-item"><strong>Successful:</strong> {$successful}</div>
        <div class="summary-item"><strong>Failed:</strong> {$failed}</div>
        <div class="summary-item"><strong>Success Rate:</strong> {$successRate}%</div>
    </div>
    
    <h2>Test Results</h2>
HTML;

        foreach ($this->results as $result) {
            $success = $result['success'] ?? false;
            $class = $success ? 'pass' : 'fail';
            $status = $success ? 'PASS' : 'FAIL';
            
            $title = '';
            if (isset($result['framework'])) {
                $title = "{$result['framework']} {$result['framework_version']} (PHP {$result['php_version']})";
            } elseif (isset($result['php_version'])) {
                $title = "PHP {$result['php_version']}";
            } else {
                $title = "Test";
            }

            $html .= <<<HTML
    <div class="result {$class}">
        <h3>{$title} <span class="status {$class}">{$status}</span></h3>
HTML;

            if (isset($result['error']) && $result['error']) {
                $error = htmlspecialchars($result['error']);
                $html .= <<<HTML
        <div class="error"><strong>Error:</strong> {$error}</div>
HTML;
            }

            if (isset($result['test_results']) && is_array($result['test_results'])) {
                $html .= '<h4>Test Scripts</h4>';
                foreach ($result['test_results'] as $testName => $testResult) {
                    $testSuccess = $testResult['success'] ?? false;
                    $testClass = $testSuccess ? 'pass' : 'fail';
                    $testStatus = $testSuccess ? 'PASS' : 'FAIL';
                    $testNameEscaped = htmlspecialchars($testName);
                    $html .= <<<HTML
        <div class="test-script">
            <strong>{$testNameEscaped}</strong> <span class="status {$testClass}">{$testStatus}</span>
HTML;
                    if (isset($testResult['error']) && $testResult['error']) {
                        $testError = htmlspecialchars($testResult['error']);
                        $html .= <<<HTML
            <div class="error">{$testError}</div>
HTML;
                    }
                    $html .= '</div>';
                }
            }

            if (isset($result['output']) && $result['output']) {
                $output = htmlspecialchars($result['output']);
                $html .= <<<HTML
        <h4>Output</h4>
        <div class="output">{$output}</div>
HTML;
            }

            $html .= '</div>';
        }

        $html .= <<<HTML
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Generate summary statistics.
     */
    private function generateSummary(): array
    {
        $total = count($this->results);
        $successful = count(array_filter($this->results, fn($r) => ($r['success'] ?? false) === true));
        $failed = $total - $successful;

        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Format date for display.
     */
    private function formatDate(): string
    {
        return date('Y-m-d H:i:s');
    }
}

