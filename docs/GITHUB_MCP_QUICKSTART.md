# GitHub MCP Server - Quick Start

## Szybki start w 5 minut

### 1. Pobierz GitHub MCP Server

**Opcja A: Docker** (jeśli masz już Docker)

```bash
docker pull ghcr.io/github/github-mcp-server
```

**Opcja B: Binarny plik** (prostsze, jeśli nie masz Dockera)

```bash
# macOS Apple Silicon (M1/M2/M3)
cd ~
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server_Darwin_arm64.tar.gz -o github-mcp-server.tar.gz
tar -xzf github-mcp-server.tar.gz
chmod +x github-mcp-server

# macOS Intel
cd ~
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server_Darwin_x86_64.tar.gz -o github-mcp-server.tar.gz
tar -xzf github-mcp-server.tar.gz
chmod +x github-mcp-server
```

> 💡 **Wskazówka:** Jeśli masz problemy z instalacją, zobacz [GITHUB_MCP_SETUP.md](GITHUB_MCP_SETUP.md)

### 2. Utwórz Personal Access Token

1. Przejdź do: https://github.com/settings/tokens
2. Kliknij "Generate new token (classic)"
3. Wybierz uprawnienia (scope'y):

   **Minimalne wymagane uprawnienia:**
   - ✅ `repo` - Full control of private repositories
     - *To obejmuje: issues, pull requests, files, branches, commits, itp.*
   - ✅ `workflow` - Update GitHub Action workflows
     - *Potrzebne do monitorowania i zarządzania GitHub Actions*

   **Dodatkowe (opcjonalne):**
   - `public_repo` - Access public repositories (jeśli repo jest publiczne)
     - *Użyj tego zamiast `repo` jeśli chcesz tylko dostęp do publicznych repo*
   - `read:org` - Read org and team membership (jeśli pracujesz z organizacjami)

4. Skopiuj token (zaczyna się od `ghp_`)

### 3. Skonfiguruj w Cursor

Otwórz ustawienia Cursor (Settings → Features → Model Context Protocol) i dodaj:

**Opcja A: Docker** (jeśli wybrałeś Docker)

```json
{
  "mcpServers": {
    "github": {
      "command": "docker",
      "args": [
        "run",
        "-i",
        "--rm",
        "-e", "GITHUB_PERSONAL_ACCESS_TOKEN=ghp_your_token_here",
        "ghcr.io/github/github-mcp-server"
      ],
      "env": {}
    }
  }
}
```

**Opcja B: Binarny plik** (jeśli wybrałeś binarny plik - prostsze!)

```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "args": ["stdio"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_your_token_here"
      }
    }
  }
}
```

> ⚠️ **WAŻNE:** 
> - ✅ **Dodaj `"args": ["stdio"]`** - to jest kluczowe! Bez tego serwer nie będzie działał
> - Zamień `ghp_your_token_here` na swój rzeczywisty token
> - Dla binarnego pliku użyj pełnej ścieżki (nie `~/github-mcp-server`)
> - Jeśli widzisz błąd "ENOENT", zobacz [GITHUB_MCP_SETUP.md](GITHUB_MCP_SETUP.md)
> - Jeśli widzisz błędy JSON, zobacz [GITHUB_MCP_FIX_STDIO.md](GITHUB_MCP_FIX_STDIO.md)

### 4. Przetestuj integrację

W Cursor możesz teraz poprosić asystenta AI:

```
"Sprawdź status ostatniego workflow run w repozytorium php-compatibility-tester"
```

```
"Utwórz issue z tytułem 'Test MCP Integration' i opisem 'To jest test'"
```

```
"Pokaż mi wszystkie otwarte issues w repozytorium"
```

## Przykłady użycia dla PHP Compatibility Tester

### Automatyczne komentarze w PR

Po uruchomieniu testów, możesz poprosić asystenta:

```
"Po wygenerowaniu raportu testów kompatybilności, dodaj komentarz do PR #123 z wynikami"
```

Asystent automatycznie:
1. Wygeneruje raport
2. Sformatuje go jako komentarz
3. Doda komentarz do PR używając MCP

### Tworzenie issues z błędami

```
"Jeśli testy kompatybilności wykryją błędy, utwórz issue z tytułem 'Compatibility Issues' i oznacz je labelami 'compatibility' i 'bug'"
```

### Monitorowanie workflow

```
"Sprawdź status wszystkich workflow runs z ostatnich 7 dni i pokaż mi te, które się nie powiodły"
```

### Aktualizacja dokumentacji

```
"Zaktualizuj badge kompatybilności w README.md na podstawie wyników testów"
```

## Najczęściej używane komendy MCP

### Issues
- `create_issue` - Tworzenie issue
- `list_issues` - Lista issues
- `add_issue_comment` - Komentarz w issue
- `add_labels_to_issue` - Dodawanie labeli

### Pull Requests
- `create_pull_request` - Tworzenie PR
- `get_pull_request` - Szczegóły PR
- `add_pull_request_comment` - Komentarz w PR

### Workflows
- `list_workflow_runs` - Lista workflow runs
- `get_workflow_run` - Szczegóły workflow
- `get_workflow_run_logs` - Logi workflow

### Files
- `get_file_content` - Pobieranie pliku
- `update_file` - Aktualizacja pliku
- `create_branch` - Tworzenie brancha

## Tryby bezpieczeństwa

### Read-Only Mode
Tylko odczyt - bezpieczne do testów:

```json
{
  "mcpServers": {
    "github": {
      "command": "/path/to/github-mcp-server",
      "args": ["--read-only"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "your-token"
      }
    }
  }
}
```

### Lockdown Mode
Ogranicza dostęp do publicznych repozytoriów:

```json
{
  "mcpServers": {
    "github": {
      "command": "/path/to/github-mcp-server",
      "args": ["--lockdown-mode"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "your-token"
      }
    }
  }
}
```

## Troubleshooting

### Problem: "Authentication failed"
- Sprawdź czy token jest poprawny
- Upewnij się że token ma wymagane uprawnienia
- Sprawdź czy token nie wygasł

### Problem: "Command not found"
- Upewnij się że ścieżka do binarnego jest absolutna
- Sprawdź czy plik ma uprawnienia do wykonania (`chmod +x`)

### Problem: "Permission denied"
- Sprawdź uprawnienia tokena
- Upewnij się że masz dostęp do repozytorium

## Następne kroki

- **Masz problem z konfiguracją?** → [GITHUB_MCP_SETUP.md](GITHUB_MCP_SETUP.md)
- Przeczytaj pełną dokumentację: [GITHUB_MCP_INTEGRATION.md](GITHUB_MCP_INTEGRATION.md)
- Sprawdź wymagane uprawnienia: [GITHUB_MCP_PERMISSIONS.md](GITHUB_MCP_PERMISSIONS.md)
- Zobacz przykłady kodu: [examples/github-integration.php](../examples/github-integration.php)
- Odwiedź oficjalną dokumentację: https://github.com/github/github-mcp-server

