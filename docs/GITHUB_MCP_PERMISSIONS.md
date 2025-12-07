# GitHub MCP Server - Wymagane Uprawnienia Tokena

## Przegląd

Ten dokument szczegółowo opisuje, które uprawnienia (scope'y) są potrzebne dla GitHub MCP Server w zależności od tego, jakie funkcje chcesz używać.

## Minimalne Wymagane Uprawnienia

### Dla podstawowych operacji (Issues, PR, Files, Branches)

**✅ `repo`** - Full control of private repositories

To uprawnienie obejmuje:
- ✅ Issues (tworzenie, edycja, komentarze, label'e)
- ✅ Pull Requests (tworzenie, edycja, komentarze, merge)
- ✅ Files (odczyt, zapis, aktualizacja)
- ✅ Branches (tworzenie, usuwanie)
- ✅ Commits (odczyt, tworzenie)
- ✅ Deployments
- ✅ Status checks
- ✅ Webhooks

**✅ `workflow`** - Update GitHub Action workflows

To uprawnienie obejmuje:
- ✅ Listowanie workflow runs
- ✅ Pobieranie szczegółów workflow runs
- ✅ Pobieranie logów z workflow
- ✅ Zarządzanie workflow

## Mapowanie Funkcji MCP na Uprawnienia

### Issues

| Funkcja MCP | Wymagane uprawnienie |
|------------|---------------------|
| `create_issue` | `repo` |
| `get_issue` | `repo` |
| `list_issues` | `repo` |
| `add_issue_comment` | `repo` |
| `add_labels_to_issue` | `repo` |
| `search_issues` | `repo` |

### Pull Requests

| Funkcja MCP | Wymagane uprawnienie |
|------------|---------------------|
| `create_pull_request` | `repo` |
| `get_pull_request` | `repo` |
| `list_pull_requests` | `repo` |
| `add_pull_request_comment` | `repo` |
| `merge_pull_request` | `repo` |
| `search_pull_requests` | `repo` |

### Repositories

| Funkcja MCP | Wymagane uprawnienie |
|------------|---------------------|
| `get_repository` | `repo` lub `public_repo` |
| `list_repositories` | `repo` lub `public_repo` |
| `create_branch` | `repo` |
| `update_file` | `repo` |
| `get_file_content` | `repo` lub `public_repo` |
| `search_code` | `repo` lub `public_repo` |

### Workflows

| Funkcja MCP | Wymagane uprawnienie |
|------------|---------------------|
| `list_workflow_runs` | `workflow` |
| `get_workflow_run` | `workflow` |
| `get_workflow_run_logs` | `workflow` |

### Stargazers

| Funkcja MCP | Wymagane uprawnienie |
|------------|---------------------|
| `list_starred_repositories` | `public_repo` (dla publicznych) lub `repo` |
| `star_repository` | `public_repo` (dla publicznych) lub `repo` |
| `unstar_repository` | `public_repo` (dla publicznych) lub `repo` |

## Konfiguracje dla Różnych Scenariuszy

### Scenariusz 1: Tylko Publiczne Repozytoria (Read-Only)

Jeśli chcesz tylko czytać dane z publicznych repozytoriów:

```
✅ public_repo - Access public repositories
```

**Uwaga:** To nie pozwoli na:
- Tworzenie issues/PR
- Aktualizację plików
- Zarządzanie workflow

### Scenariusz 2: Pełny Dostęp do Własnych Repozytoriów

Dla pełnego dostępu do swoich repozytoriów (publicznych i prywatnych):

```
✅ repo - Full control of private repositories
✅ workflow - Update GitHub Action workflows
```

### Scenariusz 3: Tylko Monitorowanie (Read-Only)

Jeśli chcesz tylko monitorować workflow i czytać dane:

```
✅ public_repo - Access public repositories (lub repo dla prywatnych)
✅ workflow - Update GitHub Action workflows
```

**Uwaga:** Użyj również flagi `--read-only` przy uruchamianiu MCP Server.

### Scenariusz 4: Praca z Organizacjami

Jeśli pracujesz z repozytoriami organizacji:

```
✅ repo - Full control of private repositories
✅ workflow - Update GitHub Action workflows
✅ read:org - Read org and team membership (opcjonalne, dla dodatkowych informacji)
```

## Bezpieczeństwo - Zasada Minimalnych Uprawnień

### ✅ DOBRZE: Minimalne uprawnienia

```json
{
  "mcpServers": {
    "github": {
      "command": "/path/to/github-mcp-server",
      "args": ["--read-only"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "token-z-public_repo"
      }
    }
  }
}
```

### ❌ ŹLE: Zbyt wiele uprawnień

```json
{
  "mcpServers": {
    "github": {
      "command": "/path/to/github-mcp-server",
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "token-z-wszystkimi-uprawnieniami"
      }
    }
  }
}
```

## Rekomendowane Uprawnienia dla PHP Compatibility Tester

Dla projektu PHP Compatibility Tester, które wymaga:

1. **Tworzenia issues z wynikami testów** → `repo`
2. **Komentowania w PR** → `repo`
3. **Monitorowania workflow** → `workflow`
4. **Aktualizowania README** → `repo`

**Rekomendowana konfiguracja:**

```
✅ repo - Full control of private repositories
✅ workflow - Update GitHub Action workflows
```

## Sprawdzanie Uprawnień Tokena

Możesz sprawdzić uprawnienia swojego tokena:

1. Przejdź do: https://github.com/settings/tokens
2. Kliknij na swój token
3. Zobaczysz listę zaznaczonych scope'ów

Lub użyj GitHub API:

```bash
curl -H "Authorization: token ghp_your_token" https://api.github.com/user
```

## Rotacja Tokenów

**Best Practice:** Rotuj tokeny regularnie (co 90 dni):

1. Utwórz nowy token z tymi samymi uprawnieniami
2. Zaktualizuj konfigurację w Cursor
3. Usuń stary token

## Troubleshooting - Problemy z Uprawnieniami

### Problem: "Resource not accessible by integration"

**Przyczyna:** Token nie ma wymaganego uprawnienia `repo`

**Rozwiązanie:** Dodaj scope `repo` do tokena

### Problem: "Workflow not found"

**Przyczyna:** Token nie ma uprawnienia `workflow`

**Rozwiązanie:** Dodaj scope `workflow` do tokena

### Problem: "Not Found" przy próbie dostępu do prywatnego repo

**Przyczyna:** Token ma tylko `public_repo`, a repo jest prywatne

**Rozwiązanie:** Zmień na scope `repo`

## Podsumowanie

| Cel | Wymagane Uprawnienia |
|-----|---------------------|
| Podstawowe operacje (issues, PR, files) | `repo` |
| Monitorowanie workflow | `workflow` |
| Tylko publiczne repo (read-only) | `public_repo` |
| Pełny dostęp | `repo` + `workflow` |
| Praca z organizacjami | `repo` + `workflow` + `read:org` (opcjonalne) |

## Linki

- [GitHub Personal Access Tokens Documentation](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens)
- [GitHub OAuth Scopes](https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/scopes-for-oauth-apps)
- [GitHub MCP Server](https://github.com/github/github-mcp-server)

