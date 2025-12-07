# FAQ - Często Zadawane Pytania

## 1. Gdzie jest plik `.compatibility.yml`?

**Odpowiedź**: Plik `.compatibility.yml` **nie istnieje** w głównym projekcie - to jest plik konfiguracyjny który **użytkownik tworzy** w swoim projekcie.

**Szablony i przykłady:**
- `templates/config/.compatibility.yml.example` - przykład konfiguracji
- `tests/fixtures/test-package/.compatibility.yml` - przykład dla testów

**Jak utworzyć:**
```bash
vendor/bin/compatibility-tester init
```

To polecenie skopiuje `.compatibility.yml.example` do `.compatibility.yml` w Twoim projekcie.

## 2. Który model AI ma najnowsze informacje?

**Modele z aktualizacjami (2024/2025):**

| Model | Aktualizacje do | Web Search | Rekomendacja |
|-------|----------------|-----------|--------------|
| GPT-4o | Październik 2024 | ✅ | Najnowszy OpenAI |
| Claude 3.5 Sonnet | 2024 | ✅ | Dobry balans |
| GPT-4 Turbo | Kwiecień 2024 | ✅ | Stabilny |
| Claude 3 Opus | 2024 | ✅ | Zaawansowany |

**Dla najnowszych informacji (2025+):**
- Użyj modeli z **web search** (np. modele z dostępem do internetu)
- Sprawdź oficjalne źródła:
  - PHP: https://www.php.net/
  - Laravel: https://laravel.com/docs
  - Symfony: https://symfony.com/doc/current/index.html
- Użyj narzędzi do wyszukiwania w czasie rzeczywistym

**W Cursor:**
- Modele z web search mają dostęp do aktualnych informacji
- Możesz użyć `web_search` tool do sprawdzenia najnowszych wersji

## 3. Skąd wiem że frameworki są "obsługiwane" jeśli nie zostały przetestowane?

**WAŻNE**: Poprawiam terminologię - frameworki nie są "obsługiwane", tylko **"konfigurowalne"**.

### Dlaczego wszystkie frameworki są "konfigurowalne"?

Kod źródłowy jest **całkowicie generyczny** - nie ma żadnych hardcoded nazw frameworków!

**Sprawdź kod w `src/FrameworkTester.php`:**

```php
// Linia 132: Używa install_command z konfiguracji
$installCommand = $frameworkConfig['install_command'];

// Linia 138-140: Parsuje i uruchamia dowolne polecenie
$commandParts = explode(' ', $installCommand);
$command = array_shift($commandParts);
$process = new Process(array_merge([$command], $commandParts));
```

**Co to oznacza?**
- Kod nie sprawdza nazwy frameworka
- Używa tylko `install_command` z konfiguracji YAML
- Każdy framework z `composer create-project` może być użyty
- **Nie ma ograniczeń w kodzie!**

### Różnica: Konfigurowalny vs. Przetestowany

| Status | Znaczenie | Przykład |
|--------|-----------|----------|
| **Konfigurowalny** | Można dodać do `.compatibility.yml` i powinno działać | Wszystkie 9 frameworków |
| **Przetestowany** | Zweryfikowany działaniem w CI/CD | Laravel, Symfony, Slim |
| **Obsługiwany** | ❌ Niepoprawna terminologia - nie używamy tego |

### Dlaczego tylko 3 są przetestowane?

1. **Czas CI/CD**: Testowanie wszystkich 9 frameworków zajęłoby ~2-3 godziny
2. **Zasoby**: Wymaga dużo pamięci i czasu
3. **Priorytet**: Laravel, Symfony, Slim to najpopularniejsze
4. **Weryfikacja**: Jeśli te 3 działają, kod generyczny powinien działać dla wszystkich

### Jak przetestować pozostałe frameworki?

**Opcja 1: Lokalnie**
```bash
vendor/bin/compatibility-tester test --framework=codeigniter
```

**Opcja 2: Dodać do CI/CD**
Edytuj `.github/workflows/self-test.yml` i dodaj do matrix:
```yaml
framework: [laravel, symfony, slim, codeigniter, cakephp]
```

**Opcja 3: Użyć w swoim projekcie**
Po prostu dodaj framework do `.compatibility.yml` - kod jest generyczny, powinno działać!

## Podsumowanie

1. **`.compatibility.yml`** - tworzy użytkownik przez `compatibility-tester init`
2. **Modele AI** - GPT-4o lub Claude 3.5 Sonnet z web search dla najnowszych informacji
3. **Frameworki** - wszystkie są **konfigurowalne** (kod generyczny), tylko 3 są **przetestowane** w CI/CD

