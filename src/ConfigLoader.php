<?php

declare(strict_types=1);

namespace LukaszZychal\PhpCompatibilityTester;

use LukaszZychal\PhpCompatibilityTester\Exception\ConfigurationException;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads and validates compatibility configuration from YAML files.
 *
 * @author Łukasz Zychal <lukasz.zychal.dev@gmail.com>
 */
class ConfigLoader
{
    private array $config = [];

    /**
     * Load configuration from a YAML file.
     *
     * @param string $configPath Path to the .compatibility.yml file
     * @return array Loaded configuration
     * @throws ConfigurationException
     */
    public function load(string $configPath): array
    {
        if (!file_exists($configPath)) {
            throw new ConfigurationException("Configuration file not found: {$configPath}");
        }

        if (!is_readable($configPath)) {
            throw new ConfigurationException("Configuration file is not readable: {$configPath}");
        }

        try {
            $content = file_get_contents($configPath);
            if ($content === false) {
                throw new ConfigurationException("Failed to read configuration file: {$configPath}");
            }

            $this->config = Yaml::parse($content) ?? [];
            $this->validate();

            return $this->config;
        } catch (\Exception $e) {
            if ($e instanceof ConfigurationException) {
                throw $e;
            }
            throw new ConfigurationException("Failed to parse configuration file: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Validate the loaded configuration.
     *
     * @throws ConfigurationException
     */
    private function validate(): void
    {
        if (empty($this->config)) {
            throw new ConfigurationException("Configuration file is empty");
        }

        if (!isset($this->config['package_name'])) {
            throw new ConfigurationException("Missing required field: package_name");
        }

        if (!isset($this->config['php_versions']) || !is_array($this->config['php_versions'])) {
            throw new ConfigurationException("Missing or invalid field: php_versions (must be an array)");
        }

        if (empty($this->config['php_versions'])) {
            throw new ConfigurationException("php_versions array cannot be empty");
        }

        if (isset($this->config['frameworks']) && !is_array($this->config['frameworks'])) {
            throw new ConfigurationException("frameworks must be an array if provided");
        }

        if (isset($this->config['test_scripts']) && !is_array($this->config['test_scripts'])) {
            throw new ConfigurationException("test_scripts must be an array if provided");
        }

        // Validate framework configurations
        if (isset($this->config['frameworks'])) {
            foreach ($this->config['frameworks'] as $framework => $frameworkConfig) {
                if (!is_array($frameworkConfig)) {
                    throw new ConfigurationException("Framework '{$framework}' configuration must be an array");
                }

                if (!isset($frameworkConfig['versions']) || !is_array($frameworkConfig['versions'])) {
                    throw new ConfigurationException("Framework '{$framework}' missing or invalid 'versions' field");
                }

                if (empty($frameworkConfig['versions'])) {
                    throw new ConfigurationException("Framework '{$framework}' versions array cannot be empty");
                }

                if (!isset($frameworkConfig['install_command'])) {
                    throw new ConfigurationException("Framework '{$framework}' missing required 'install_command' field");
                }

                if (!isset($frameworkConfig['php_min_version'])) {
                    throw new ConfigurationException("Framework '{$framework}' missing required 'php_min_version' field");
                }
            }
        }

        // Validate test scripts
        if (isset($this->config['test_scripts'])) {
            foreach ($this->config['test_scripts'] as $index => $script) {
                if (!is_array($script)) {
                    throw new ConfigurationException("Test script at index {$index} must be an array");
                }

                if (!isset($script['name'])) {
                    throw new ConfigurationException("Test script at index {$index} missing required 'name' field");
                }

                if (!isset($script['script'])) {
                    throw new ConfigurationException("Test script '{$script['name']}' missing required 'script' field");
                }
            }
        }
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
}

