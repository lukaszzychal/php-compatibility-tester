# Integracja z GitHub MCP Server

## Wprowadzenie

GitHub MCP Server umożliwia asystentom AI (jak Cursor AI) bezpośrednią interakcję z GitHubem poprzez Model Context Protocol (MCP). Dzięki tej integracji możesz automatyzować wiele zadań związanych z zarządzaniem repozytorium, raportowaniem wyników testów i współpracą.

## Co daje integracja?

### 1. Automatyzacja zarządzania repozytorium
- ✅ Tworzenie i zarządzanie issues
- ✅ Automatyczne komentarze w Pull Requestach
- ✅ Zarządzanie labelami i milestone'ami
- ✅ Tworzenie i zarządzanie branchami

### 2. Integracja z GitHub Actions
- ✅ Monitorowanie statusu workflow
- ✅ Pobieranie logów z CI/CD
- ✅ Analiza wyników testów
- ✅ Automatyczne triggerowanie workflow

### 3. Raportowanie wyników testów
- ✅ Automatyczne tworzenie issues z wynikami
- ✅ Komentarze w PR z podsumowaniem testów
- ✅ Aktualizacja badge'ów w README
- ✅ Generowanie release notes

### 4. Współpraca i komunikacja
- ✅ Zarządzanie współpracownikami
- ✅ Kategoryzowanie issues
- ✅ Automatyczne tagowanie odpowiednich osób

## Instalacja i konfiguracja

### Krok 1: Instalacja GitHub MCP Server

#### Opcja A: Użycie binarnego pliku
```bash
# Pobierz najnowszą wersję z GitHub Releases
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server-darwin-amd64 -o github-mcp-server
chmod +x github-mcp-server
```

#### Opcja B: Użycie Dockera
```bash
docker pull ghcr.io/github/github-mcp-server
```

### Krok 2: Konfiguracja w Cursor

1. Otwórz ustawienia Cursor
2. Przejdź do sekcji MCP Servers
3. Dodaj konfigurację:

```json
{
  "mcpServers": {
    "github": {
      "command": "/path/to/github-mcp-server",
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "your-token-here"
      }
    }
  }
}
```

### Krok 3: Utworzenie Personal Access Token

1. Przejdź do GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Wygeneruj nowy token z następującymi uprawnieniami (scope'y):

   **Wymagane uprawnienia:**
   - ✅ `repo` - Full control of private repositories
     - *Obejmuje: issues, pull requests, files, branches, commits, deployments, status, hooks*
     - *To jest główne uprawnienie potrzebne do większości operacji*
   
   - ✅ `workflow` - Update GitHub Action workflows
     - *Potrzebne do: monitorowania workflow runs, pobierania logów, zarządzania workflow*

   **Opcjonalne uprawnienia:**
   - `public_repo` - Access public repositories
     - *Użyj zamiast `repo` jeśli chcesz tylko dostęp do publicznych repozytoriów*
   
   - `read:org` - Read org and team membership
     - *Jeśli pracujesz z repozytoriami organizacji*

   **Uwaga:** W GitHub Personal Access Tokens (classic) nie ma osobnych scope'ów dla `issues` i `pull_requests` - są one częścią scope'a `repo`.

## Przykłady użycia dla PHP Compatibility Tester

### Przykład 1: Automatyczne raportowanie wyników testów

Po uruchomieniu testów kompatybilności, możesz automatycznie:

```php
// W ReportGenerator lub nowej klasie GitHubReporter
public function reportToGitHub(array $results, string $prNumber = null): void
{
    // Generuj raport
    $report = $this->generate('markdown');
    
    // Jeśli jesteśmy w kontekście PR, dodaj komentarz
    if ($prNumber) {
        // Asystent AI może użyć: add_issue_comment
        // z parametrami: owner, repo, issue_number, body
    }
    
    // Jeśli są błędy, utwórz issue
    $failedTests = array_filter($results, fn($r) => !($r['success'] ?? false));
    if (!empty($failedTests)) {
        // Asystent AI może użyć: create_issue
        // z parametrami: owner, repo, title, body, labels
    }
}
```

**Jak to działa z MCP:**
- Asystent AI może automatycznie wywołać `add_issue_comment` po wygenerowaniu raportu
- Może utworzyć issue z wynikami testów używając `create_issue`
- Może zaktualizować label'e używając `add_labels_to_issue`

### Przykład 2: Integracja z GitHub Actions

Możesz rozszerzyć workflow o automatyczne raportowanie:

```yaml
# .github/workflows/compatibility-tests.yml
name: Compatibility Tests

on:
  pull_request:
    paths:
      - 'composer.json'
      - 'src/**'

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Run compatibility tests
        run: vendor/bin/compatibility-tester test
      
      - name: Generate report
        run: vendor/bin/compatibility-tester report --format=markdown --output=report.md
      
      - name: Upload report
        uses: actions/upload-artifact@v4
        with:
          name: compatibility-report
          path: report.md
```

**Z MCP możesz:**
- Sprawdzić status workflow: `get_workflow_run`
- Pobrać logi: `get_workflow_run_logs`
- Sprawdzić wszystkie workflow: `list_workflow_runs`

### Przykład 3: Automatyczne tworzenie issues z błędami

Gdy testy wykryją problemy kompatybilności:

```php
// Nowa klasa: src/GitHubReporter.php
class GitHubReporter
{
    public function createCompatibilityIssue(array $failedTests): void
    {
        $title = "Compatibility Issues Detected";
        $body = $this->formatIssueBody($failedTests);
        $labels = ['compatibility', 'bug', 'automated'];
        
        // Asystent AI może użyć:
        // create_issue(owner, repo, title, body, labels)
    }
    
    private function formatIssueBody(array $failedTests): string
    {
        $body = "## Compatibility Test Failures\n\n";
        $body .= "The following compatibility tests have failed:\n\n";
        
        foreach ($failedTests as $test) {
            $body .= sprintf(
                "- **%s %s** (PHP %s): %s\n",
                $test['framework'],
                $test['framework_version'],
                $test['php_version'],
                $test['error'] ?? 'Unknown error'
            );
        }
        
        return $body;
    }
}
```

### Przykład 4: Aktualizacja README z wynikami

Możesz automatycznie aktualizować badge'e i statusy w README:

```php
public function updateReadmeBadges(array $results): void
{
    $readme = file_get_contents('README.md');
    
    // Oblicz statystyki
    $total = count($results);
    $successful = count(array_filter($results, fn($r) => $r['success'] ?? false));
    $successRate = round(($successful / $total) * 100, 2);
    
    // Zaktualizuj badge
    $badge = sprintf(
        '![Compatibility](https://img.shields.io/badge/Compatibility-%s%%25-%s)',
        $successRate,
        $successRate >= 90 ? 'green' : ($successRate >= 70 ? 'yellow' : 'red')
    );
    
    // Asystent AI może użyć:
    // update_file(owner, repo, path, message, content, branch)
    // aby zaktualizować README.md
}
```

### Przykład 5: Monitorowanie i analityka

Możesz śledzić trendy w testach kompatybilności:

```php
public function analyzeCompatibilityTrends(): array
{
    // Pobierz wszystkie workflow runs
    // Asystent AI może użyć: list_workflow_runs
    
    // Pobierz issues związane z kompatybilnością
    // Asystent AI może użyć: search_issues z query: "label:compatibility"
    
    // Analizuj trendy
    return [
        'total_tests' => 0,
        'success_rate_trend' => [],
        'common_failures' => [],
    ];
}
```

## Konkretne scenariusze użycia

### Scenariusz 1: PR z wynikami testów

**Sytuacja:** Developer tworzy PR, który zmienia zależności w `composer.json`

**Co się dzieje:**
1. GitHub Actions uruchamia testy kompatybilności
2. Testy wykrywają problem z Laravel 12.*
3. Asystent AI automatycznie:
   - Dodaje komentarz do PR z wynikami
   - Tworzy issue z szczegółami błędu
   - Oznacza odpowiednie osoby

**Komendy MCP:**
```javascript
// Dodaj komentarz do PR
add_issue_comment({
  owner: "lukaszzychal",
  repo: "php-compatibility-tester",
  issue_number: 123,
  body: "## Compatibility Test Results\n\n❌ Laravel 12.* failed..."
})

// Utwórz issue
create_issue({
  owner: "lukaszzychal",
  repo: "php-compatibility-tester",
  title: "Compatibility issue: Laravel 12.*",
  body: "Detailed error information...",
  labels: ["compatibility", "laravel"]
})
```

### Scenariusz 2: Cotygodniowy raport

**Sytuacja:** Chcesz cotygodniowy przegląd statusu kompatybilności

**Co się dzieje:**
1. Cron job uruchamia testy
2. Generuje raport
3. Asystent AI:
   - Tworzy issue z raportem
   - Aktualizuje wiki/dokumentację
   - Wysyła powiadomienia

**Komendy MCP:**
```javascript
// Utwórz issue z raportem
create_issue({
  owner: "lukaszzychal",
  repo: "php-compatibility-tester",
  title: "Weekly Compatibility Report - " + new Date().toISOString().split('T')[0],
  body: reportMarkdown,
  labels: ["report", "automated"]
})
```

### Scenariusz 3: Automatyczne poprawki

**Sytuacja:** Testy wykrywają prosty problem, który można naprawić automatycznie

**Co się dzieje:**
1. Testy wykrywają brakujące zależności
2. Asystent AI analizuje błąd
3. Tworzy PR z poprawką

**Komendy MCP:**
```javascript
// Utwórz branch
create_branch({
  owner: "lukaszzychal",
  repo: "php-compatibility-tester",
  branch: "fix/compatibility-issue-123",
  sha: "main-branch-sha"
})

// Utwórz PR
create_pull_request({
  owner: "lukaszzychal",
  repo: "php-compatibility-tester",
  title: "Fix: Add missing dependency for Laravel 12.*",
  head: "fix/compatibility-issue-123",
  base: "main",
  body: "Automatically fixed compatibility issue..."
})
```

## Tryby pracy

### Read-Only Mode
Bezpieczny tryb tylko do odczytu:
```bash
./github-mcp-server --read-only
```

### Lockdown Mode
Ogranicza dostęp do publicznych repozytoriów:
```bash
./github-mcp-server --lockdown-mode
```

## Dostępne narzędzia MCP

### Issues
- `create_issue` - Tworzenie issues
- `get_issue` - Pobieranie issue
- `list_issues` - Lista issues
- `add_issue_comment` - Dodawanie komentarzy
- `add_labels_to_issue` - Dodawanie labeli

### Pull Requests
- `create_pull_request` - Tworzenie PR
- `get_pull_request` - Pobieranie PR
- `list_pull_requests` - Lista PR
- `add_pull_request_comment` - Komentarze w PR
- `merge_pull_request` - Merge PR

### Workflows
- `list_workflow_runs` - Lista workflow runs
- `get_workflow_run` - Szczegóły workflow run
- `get_workflow_run_logs` - Logi workflow

### Repositories
- `get_repository` - Informacje o repo
- `list_repositories` - Lista repozytoriów
- `create_branch` - Tworzenie brancha
- `update_file` - Aktualizacja plików

### Search
- `search_code` - Wyszukiwanie w kodzie
- `search_issues` - Wyszukiwanie issues
- `search_pull_requests` - Wyszukiwanie PR

## Best Practices

1. **Używaj read-only mode** do testów i eksploracji
2. **Ogranicz uprawnienia tokena** tylko do potrzebnych zakresów
3. **Używaj labeli** do kategoryzacji automatycznych issues/PR
4. **Dodawaj kontekst** w automatycznych komentarzach
5. **Monitoruj użycie** tokena w GitHub Settings

## Bezpieczeństwo

- ✅ Nigdy nie commituj tokenów do repozytorium
- ✅ Używaj zmiennych środowiskowych
- ✅ Rotuj tokeny regularnie
- ✅ Używaj minimalnych wymaganych uprawnień
- ✅ Monitoruj aktywność tokena

## Przykładowa implementacja

Zobacz [examples/github-integration.php](examples/github-integration.php) dla pełnego przykładu integracji.

## Wsparcie

- [Instrukcja konfiguracji i rozwiązywanie problemów](GITHUB_MCP_SETUP.md) - **Zacznij tutaj jeśli masz problemy!**
- [GitHub MCP Server Documentation](https://github.com/github/github-mcp-server)
- [MCP Protocol Specification](https://modelcontextprotocol.io)
- [GitHub API Documentation](https://docs.github.com/en/rest)
- [Szczegółowy przewodnik po uprawnieniach tokena](GITHUB_MCP_PERMISSIONS.md)

