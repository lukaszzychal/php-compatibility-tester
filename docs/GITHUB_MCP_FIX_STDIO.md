# Naprawa błędu: "Unexpected token 'A', "A GitHub M"... is not valid JSON"

## Problem

Jeśli widzisz błędy w logach MCP:
```
[error] Client error for command Unexpected token 'A', "A GitHub M"... is not valid JSON
[error] Client error for command Unexpected token 'U', "Usage:" is not valid JSON
```

To oznacza, że **brakuje argumentu `stdio`** w konfiguracji!

## Rozwiązanie

Musisz dodać `"args": ["stdio"]` do konfiguracji.

### ❌ Błędna konfiguracja (bez `stdio`):

```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_token"
      }
    }
  }
}
```

### ✅ Poprawna konfiguracja (z `stdio`):

```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "args": ["stdio"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_token"
      }
    }
  }
}
```

## Dlaczego to jest potrzebne?

GitHub MCP Server ma kilka trybów pracy:
- `stdio` - komunikacja przez stdin/stdout (dla MCP)
- `help` - wyświetla pomoc
- `version` - wyświetla wersję

Cursor potrzebuje trybu `stdio`, aby komunikować się z serwerem przez JSON-RPC.

## Jak naprawić?

1. Otwórz ustawienia Cursor: **Settings** → **Features** → **Model Context Protocol**
2. Znajdź konfigurację `github`
3. Dodaj `"args": ["stdio"]` (jeśli nie ma)
4. Zapisz i zrestartuj Cursor

## Sprawdzenie

Po naprawie, w logach MCP powinieneś zobaczyć:
```
[info] Starting new stdio process with command: /Users/lukaszzychal/github-mcp-server stdio
[info] Client connected successfully
```

Zamiast błędów JSON.

## Pełna poprawna konfiguracja

### Binarny plik:
```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "args": ["stdio"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_twój_token"
      }
    }
  }
}
```

### Docker:
```json
{
  "mcpServers": {
    "github": {
      "command": "docker",
      "args": [
        "run",
        "-i",
        "--rm",
        "-e", "GITHUB_PERSONAL_ACCESS_TOKEN=ghp_twój_token",
        "ghcr.io/github/github-mcp-server",
        "stdio"
      ],
      "env": {}
    }
  }
}
```

**Uwaga:** W Dockerze `stdio` jest ostatnim argumentem w `args`, nie osobnym polem.

