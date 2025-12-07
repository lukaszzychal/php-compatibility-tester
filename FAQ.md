# FAQ - Frequently Asked Questions

## 1. Where is the `.compatibility.yml` file?

**Answer**: The `.compatibility.yml` file **does not exist** in the main project - it is a configuration file that **users create** in their project.

**Templates and examples:**
- `templates/config/.compatibility.yml.example` - configuration example
- `tests/fixtures/test-package/.compatibility.yml` - example for tests

**How to create:**
```bash
vendor/bin/compatibility-tester init
```

This command will copy `.compatibility.yml.example` to `.compatibility.yml` in your project.

## 2. Which AI model has the latest information?

**Models with updates (2024/2025):**

| Model | Updates to | Web Search | Recommendation |
|-------|------------|-----------|----------------|
| GPT-4o | October 2024 | ✅ | Latest OpenAI |
| Claude 3.5 Sonnet | 2024 | ✅ | Good balance |
| GPT-4 Turbo | April 2024 | ✅ | Stable |
| Claude 3 Opus | 2024 | ✅ | Advanced |

**For latest information (2025+):**
- Use models with **web search** (e.g., models with internet access)
- Check official sources:
  - PHP: https://www.php.net/
  - Laravel: https://laravel.com/docs
  - Symfony: https://symfony.com/doc/current/index.html
- Use real-time search tools

**In Cursor:**
- Models with web search have access to current information
- You can use `web_search` tool to check latest versions

## 3. How do I know frameworks are "supported" if they haven't been tested?

**IMPORTANT**: I'm correcting the terminology - frameworks are not "supported", they are **"configurable"**.

### Why are all frameworks "configurable"?

The source code is **completely generic** - there are no hardcoded framework names!

**Check the code in `src/FrameworkTester.php`:**

```php
// Line 132: Uses install_command from configuration
$installCommand = $frameworkConfig['install_command'];

// Line 138-140: Parses and runs any command
$commandParts = explode(' ', $installCommand);
$command = array_shift($commandParts);
$process = new Process(array_merge([$command], $commandParts));
```

**What does this mean?**
- Code doesn't check framework name
- Uses only `install_command` from YAML configuration
- Any framework with `composer create-project` can be used
- **There are no code limitations!**

### Difference: Configurable vs. Tested

| Status | Meaning | Example |
|--------|---------|---------|
| **Configurable** | Can be added to `.compatibility.yml` and should work | All 9 frameworks |
| **Tested** | Verified by operation in CI/CD | Laravel, Symfony, Slim, CodeIgniter, CakePHP |
| **Supported** | ❌ Incorrect terminology - we don't use this |

### Why are only some tested?

1. **CI/CD Time**: Testing all 9 frameworks would take ~2-3 hours
2. **Resources**: Requires a lot of memory and time
3. **Priority**: Laravel, Symfony, Slim are the most popular
4. **Verification**: If these work, generic code should work for all

### How to test remaining frameworks?

**Option 1: Locally**
```bash
vendor/bin/compatibility-tester test --framework=codeigniter
```

**Option 2: Add to CI/CD**
Edit `.github/workflows/self-test.yml` and add to matrix:
```yaml
framework: [laravel, symfony, slim, codeigniter, cakephp]
```

**Option 3: Use in your project**
Just add framework to `.compatibility.yml` - code is generic, it should work!

## Summary

1. **`.compatibility.yml`** - created by user via `compatibility-tester init`
2. **AI Models** - GPT-4o or Claude 3.5 Sonnet with web search for latest information
3. **Frameworks** - all are **configurable** (generic code), only some are **tested** in CI/CD

