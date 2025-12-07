# GitHub MCP Server - Instrukcja Konfiguracji

## Rozwiązanie problemu: "ENOENT" - Plik nie znaleziony

Jeśli widzisz błąd:
```
Client error for command Wystąpił błąd systemu (spawn /absolute/path/to/github-mcp-server ENOENT)
```

To oznacza, że ścieżka do `github-mcp-server` jest nieprawidłowa lub plik nie istnieje.

## Wybór metody: Docker vs Binarny plik

Masz dwie opcje - obie są równie dobre:

### Binarny plik (Prostsze, jeśli nie masz Dockera)
- ✅ Nie wymaga Dockera
- ✅ Szybsze uruchomienie (bez overhead kontenera)
- ✅ Mniej zależności
- ❌ Trzeba pobrać i zarządzać plikiem ręcznie

### Docker (Lepsze, jeśli już używasz Dockera)
- ✅ Działa identycznie na każdym systemie
- ✅ Łatwiejsze aktualizacje (`docker pull`)
- ✅ Nie trzeba zarządzać plikami binarnymi
- ❌ Wymaga zainstalowanego Dockera
- ❌ Wolniejsze uruchomienie (overhead kontenera)

**Rekomendacja:** Jeśli masz już Docker - użyj go. Jeśli nie - binarny plik jest prostszy!

## Opcja 1: Docker (Jeśli masz już Docker)

### Krok 1: Pobierz obraz Dockera

```bash
docker pull ghcr.io/github/github-mcp-server
```

### Krok 2: Skonfiguruj w Cursor

Otwórz ustawienia Cursor (Settings → Features → Model Context Protocol) i dodaj:

```json
{
  "mcpServers": {
    "github": {
      "command": "docker",
      "args": [
        "run",
        "-i",
        "--rm",
        "-e", "GITHUB_PERSONAL_ACCESS_TOKEN=ghp_your_token_here"
      ],
      "env": {}
    }
  }
}
```

**WAŻNE:** Zamień `ghp_your_token_here` na swój rzeczywisty token!

### Krok 3: Utwórz Personal Access Token

1. Przejdź do: https://github.com/settings/tokens
2. Kliknij "Generate new token (classic)"
3. Zaznacz uprawnienia:
   - ✅ `repo` - Full control of private repositories
   - ✅ `workflow` - Update GitHub Action workflows
4. Skopiuj token (zaczyna się od `ghp_`)

### Krok 4: Przetestuj

Zrestartuj Cursor i spróbuj zapytać asystenta:
```
"Sprawdź status ostatniego workflow run w repozytorium php-compatibility-tester"
```

## Opcja 2: Binarny plik (Prostsze - BEZ Dockera)

**To jest prostsze rozwiązanie jeśli nie używasz Dockera na co dzień!**

Jeśli wolisz użyć binarnego pliku zamiast Dockera:

### macOS (Apple Silicon - M1/M2/M3)

```bash
# Pobierz i rozpakuj
cd ~
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server_Darwin_arm64.tar.gz -o github-mcp-server.tar.gz
tar -xzf github-mcp-server.tar.gz
chmod +x github-mcp-server

# Sprawdź czy działa
./github-mcp-server --version
```

### macOS (Intel)

```bash
# Pobierz i rozpakuj
cd ~
curl -L https://github.com/github/github-mcp-server/releases/latest/download/github-mcp-server_Darwin_x86_64.tar.gz -o github-mcp-server.tar.gz
tar -xzf github-mcp-server.tar.gz
chmod +x github-mcp-server

# Sprawdź czy działa
./github-mcp-server --version
```

**Uwaga:** Po rozpakowaniu plik `github-mcp-server` będzie w katalogu domowym (`~`).

### Konfiguracja w Cursor (binarny plik)

Otwórz ustawienia Cursor (Settings → Features → Model Context Protocol) i dodaj:

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

**WAŻNE:** 
- ✅ **Dodaj `"args": ["stdio"]`** - to jest kluczowe! Bez tego serwer nie będzie działał
- Użyj pełnej ścieżki do pliku (nie `~/github-mcp-server`, ale `/Users/lukaszzychal/github-mcp-server`)
- Zamień `ghp_your_token_here` na swój rzeczywisty token
- Jeśli masz inne konto użytkownika, zamień `lukaszzychal` na swoją nazwę użytkownika

**Jak sprawdzić pełną ścieżkę:**
```bash
echo ~/github-mcp-server
# lub
realpath ~/github-mcp-server
```

## Sprawdzanie czy działa

Po konfiguracji, sprawdź logi MCP w Cursor:
- Otwórz: View → Output → wybierz "MCP"
- Powinieneś zobaczyć komunikaty o połączeniu z GitHub MCP Server

## Troubleshooting

### Problem: "docker: command not found"

**Rozwiązanie:** Zainstaluj Docker Desktop dla macOS:
- Pobierz z: https://www.docker.com/products/docker-desktop
- Zainstaluj i uruchom Docker Desktop
- Zrestartuj Cursor

### Problem: "Authentication failed"

**Rozwiązanie:**
1. Sprawdź czy token jest poprawny (zaczyna się od `ghp_`)
2. Upewnij się że token ma wymagane uprawnienia (`repo` i `workflow`)
3. Sprawdź czy token nie wygasł

### Problem: "Unexpected token 'A', "A GitHub M"... is not valid JSON"

**Przyczyna:** Brakuje argumentu `stdio` w konfiguracji

**Rozwiązanie:** Dodaj `"args": ["stdio"]` do konfiguracji:

```json
{
  "mcpServers": {
    "github": {
      "command": "/Users/lukaszzychal/github-mcp-server",
      "args": ["stdio"],  // ← TO JEST KLUCZOWE!
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "ghp_token"
      }
    }
  }
}
```

### Problem: "Permission denied"

**Rozwiązanie (dla binarnego pliku):**
```bash
chmod +x ~/github-mcp-server
```

### Problem: Docker nie może pobrać obrazu

**Rozwiązanie:**
```bash
# Sprawdź połączenie z internetem
docker pull ghcr.io/github/github-mcp-server

# Jeśli nie działa, sprawdź czy Docker Desktop jest uruchomiony
docker ps
```

## Którą opcję wybrać?

**Użyj binarnego pliku jeśli:**
- Nie masz Dockera zainstalowanego
- Chcesz najprostsze rozwiązanie
- Nie używasz Dockera na co dzień

**Użyj Dockera jeśli:**
- Masz już Docker zainstalowany i uruchomiony
- Używasz Dockera w innych projektach
- Chcesz łatwiejsze aktualizacje (`docker pull`)

**Oba rozwiązania działają identycznie!** Wybierz to, które jest dla Ciebie wygodniejsze.

## Następne kroki

Po pomyślnej konfiguracji:
- Przeczytaj [GITHUB_MCP_QUICKSTART.md](GITHUB_MCP_QUICKSTART.md) - przykłady użycia
- Zobacz [GITHUB_MCP_INTEGRATION.md](GITHUB_MCP_INTEGRATION.md) - pełna dokumentacja
- Sprawdź [GITHUB_MCP_PERMISSIONS.md](GITHUB_MCP_PERMISSIONS.md) - szczegóły uprawnień

