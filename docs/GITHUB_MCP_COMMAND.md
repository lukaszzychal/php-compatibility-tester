# GitHub MCP Server - Dokładna Konfiguracja "command"

## Odpowiedź na pytanie: "Jaka command powinna być?"

### Opcja 1: BEZ Dockera (Rekomendowane - Prostsze)

Jeśli masz już pobrany plik `github-mcp-server` w katalogu domowym:

```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "args": ["stdio"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_twój_token_tutaj"
      }
    }
  }
}
```

**⚠️ WAŻNE:** Musisz dodać `"args": ["stdio"]` - bez tego serwer nie będzie działał!

**Gdzie:**
- `command` = pełna ścieżka do pliku `github-mcp-server`
- `env.GITHUB_PERSONAL_ACCESS_TOKEN` = Twój token GitHub (zaczyna się od `ghp_`)

### Opcja 2: Z Dockerem

Jeśli wolisz użyć Dockera:

```json
{
  "mcpServers": {
    "github": {
      "command": "docker",
      "args": [
        "run",
        "-i",
        "--rm",
        "-e", "GITHUB_PERSONAL_ACCESS_TOKEN=ghp_twój_token_tutaj",
        "ghcr.io/github/github-mcp-server"
      ],
      "env": {}
    }
  }
}
```

## Jak sprawdzić swoją ścieżkę?

Uruchom w terminalu:

```bash
# Sprawdź gdzie jest plik
ls -la ~/github-mcp-server

# Jeśli istnieje, pokaż pełną ścieżkę
realpath ~/github-mcp-server
# lub po prostu:
echo ~/github-mcp-server
```

## Jeśli plik nie istnieje - Pobierz go:

### macOS Apple Silicon (M1/M2/M3):

```bash
cd ~
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server_Darwin_arm64.tar.gz -o github-mcp-server.tar.gz
tar -xzf github-mcp-server.tar.gz
chmod +x github-mcp-server
```

### macOS Intel:

```bash
cd ~
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server_Darwin_x86_64.tar.gz -o github-mcp-server.tar.gz
tar -xzf github-mcp-server.tar.gz
chmod +x github-mcp-server
```

## Przykład dla Twojego systemu

Na podstawie Twojego systemu (macOS ARM64), konfiguracja powinna wyglądać tak:

```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
      }
    }
  }
}
```

**WAŻNE:**
1. Zamień `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` na swój rzeczywisty token
2. Jeśli masz inne konto użytkownika, zamień `lukaszzychal` na swoją nazwę użytkownika
3. Użyj pełnej ścieżki (z `/Users/...`), nie skróconej (`~/...`)

## Gdzie to wkleić?

1. Otwórz Cursor
2. Przejdź do: **Settings** (⌘,) → **Features** → **Model Context Protocol**
3. Kliknij **Edit Config** lub **Add Server**
4. Wklej konfigurację JSON powyżej
5. Zapisz i zrestartuj Cursor

## Sprawdzenie czy działa

Po konfiguracji:
1. Zrestartuj Cursor
2. Otwórz: **View** → **Output** → wybierz **"MCP"**
3. Powinieneś zobaczyć komunikaty o połączeniu z GitHub MCP Server
4. Spróbuj zapytać asystenta: *"Sprawdź status ostatniego workflow run"*

## Podsumowanie

**Dla Ciebie (bez Dockera):**
```json
"command": "/Users/lukaszzychal/github-mcp-server"
```

**Z Dockerem:**
```json
"command": "docker"
```

**Odpowiedź:** NIE musisz używać Dockera! Możesz użyć bezpośrednio binarnego pliku.

