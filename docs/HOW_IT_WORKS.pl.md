# Jak to działa

Ten dokument wyjaśnia, jak biblioteka `php-compatibility-tester` weryfikuje kompatybilność Twojego pakietu z różnymi frameworkami i wersjami PHP.

## Przegląd

Tester kompatybilności działa poprzez:

1. **Tworzenie tymczasowych projektów frameworków** dla każdej wersji frameworka, którą chcesz przetestować
2. **Instalowanie Twojego pakietu** w każdym projekcie frameworka
3. **Uruchamianie skryptów testowych** w celu weryfikacji, czy Twój pakiet działa poprawnie
4. **Generowanie raportów** z wynikami testów

## Proces krok po kroku

### 1. Ładowanie konfiguracji

Gdy uruchamiasz `compatibility-tester test`, narzędzie najpierw ładuje plik konfiguracyjny `.compatibility.yml`:

```yaml
package_name: "vendor/package-name"
php_versions: ['8.1', '8.2', '8.3', '8.4']
frameworks:
  laravel:
    versions: ['11.*', '12.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
```

Klasa `ConfigLoader` waliduje tę konfigurację i zapewnia, że wszystkie wymagane pola są obecne.

### 2. Tworzenie projektu frameworka

Dla każdej kombinacji frameworka i wersji, klasa `FrameworkTester`:

1. **Tworzy tymczasowy katalog** dla projektu frameworka:
   ```
   /tmp/php-compatibility-tester/laravel-11-x-8.1/
   ```

2. **Uruchamia polecenie instalacji frameworka**:
   ```bash
   composer create-project laravel/laravel /tmp/php-compatibility-tester/laravel-11-x-8.1
   ```

3. **Weryfikuje, czy projekt został utworzony pomyślnie**

### 3. Instalacja pakietu

Po utworzeniu projektu frameworka, tester:

1. **Dodaje Twój pakiet jako lokalne repozytorium** w `composer.json` frameworka:
   ```json
   {
     "repositories": [
       {
         "type": "path",
         "url": "/path/to/your/package"
       }
     ]
   }
   ```

2. **Instaluje Twój pakiet** używając Composera:
   ```bash
   composer require vendor/package-name:dev-master
   ```

3. **Sprawdza błędy instalacji** (konflikty zależności, niekompatybilności wersji PHP, itp.)

### 4. Wykonanie skryptów testowych

Dla każdego skryptu testowego zdefiniowanego w konfiguracji:

```yaml
test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Test class autoloading'
```

Tester:

1. **Lokalizuje skrypt testowy** (względem głównego katalogu pakietu lub ścieżka bezwzględna)
2. **Uruchamia skrypt** w kontekście projektu frameworka:
   ```bash
   php tests/compatibility/check-autoload.php
   ```
3. **Przechwytuje wynik** i kod wyjścia
4. **Rejestruje sukces/porażkę** w wynikach testów

### 5. Zbieranie wyników

Wszystkie wyniki testów są zbierane do tablicy:

```php
[
  'success' => true/false,
  'error' => null lub komunikat błędu,
  'output' => 'wynik polecenia',
  'framework' => 'laravel',
  'framework_version' => '11.*',
  'php_version' => '8.1',
  'test_results' => [
    'autoloading' => [
      'success' => true,
      'output' => 'SUCCESS: All classes can be autoloaded',
      ...
    ]
  ]
]
```

### 6. Generowanie raportu

Klasa `ReportGenerator` tworzy raporty w różnych formatach:

- **Markdown**: Format czytelny dla człowieka, do dokumentacji
- **JSON**: Format czytelny dla maszyn, do integracji CI/CD
- **HTML**: Format wizualny do przeglądania w przeglądarce

## Przykład: Testowanie Laravel 11 z PHP 8.3

Oto co się dzieje, gdy testujesz swój pakiet z Laravel 11 i PHP 8.3:

1. **Konfiguracja jest ładowana** z `.compatibility.yml`
2. **Tworzony jest tymczasowy katalog**: `/tmp/php-compatibility-tester/laravel-11-x-8.3/`
3. **Tworzony jest projekt Laravel 11**:
   ```bash
   composer create-project laravel/laravel /tmp/php-compatibility-tester/laravel-11-x-8.3
   ```
4. **Twój pakiet jest dodawany do composer.json**:
   ```json
   {
     "repositories": [
       {
         "type": "path",
         "url": "/path/to/your/package"
       }
     ],
     "require": {
       "vendor/package-name": "dev-master"
     }
   }
   ```
5. **Composer instaluje Twój pakiet**:
   ```bash
   composer require vendor/package-name:dev-master
   ```
6. **Skrypty testowe są wykonywane**:
   ```bash
   php tests/compatibility/check-autoload.php
   ```
7. **Wyniki są zbierane** i dodawane do raportu

## Testowanie zależności Composera

Oprócz testowania frameworków, klasa `ComposerTester` testuje rozwiązywanie zależności:

1. **Tworzy tymczasowy composer.json** z Twoim pakietem jako zależnością
2. **Testuje rozwiązywanie zależności** dla każdej wersji PHP
3. **Wykrywa konflikty** przed uruchomieniem testów frameworków

To pomaga wcześnie wykryć problemy z zależnościami, zanim spędzisz czas na tworzeniu projektów frameworków.

## Filtrowanie

Możesz filtrować testy używając opcji wiersza poleceń:

```bash
# Testuj tylko Laravel
compatibility-tester test --framework=laravel

# Testuj tylko PHP 8.3
compatibility-tester test --php=8.3

# Testuj Laravel 11.* z PHP 8.3
compatibility-tester test --framework=laravel --version=11.* --php=8.3
```

Filtrowanie odbywa się w metodzie `CompatibilityTester::runTests()`, która pomija niepasujące kombinacje.

## Obsługa błędów

Tester obsługuje różne scenariusze błędów:

- **Tworzenie projektu frameworka nie powiodło się**: Błąd jest rejestrowany, test kontynuuje z następnym frameworkiem
- **Instalacja pakietu nie powiodła się**: Błąd jest rejestrowany ze szczegółowym wynikiem
- **Skrypt testowy nie powiódł się**: Pojedyncza porażka testu jest rejestrowana, inne testy kontynuują
- **Niezgodność wersji PHP**: Test jest pomijany z jasnym komunikatem

Wszystkie błędy są uwzględnione w końcowym raporcie, więc możesz zobaczyć dokładnie, co się nie powiodło i dlaczego.

## Czyszczenie

Domyślnie tymczasowe projekty frameworków **nie są** automatycznie usuwane. To pozwala na:

- Inspekcję nieudanych instalacji
- Ręczne debugowanie problemów
- Ponowne użycie projektów dla szybszego testowania

Możesz ręcznie wyczyścić używając:
```bash
rm -rf /tmp/php-compatibility-tester/
```

Lub zaimplementować czyszczenie w pipeline CI/CD po wygenerowaniu raportów.

## Rozważania dotyczące wydajności

- **Tworzenie projektu frameworka** jest najwolniejszym krokiem (może zająć 1-5 minut na framework)
- **Instalacja pakietu** jest stosunkowo szybka (zwykle < 30 sekund)
- **Wykonanie skryptu testowego** jest bardzo szybkie (zwykle < 5 sekund)

Tester używa `continue-on-error: true` w CI/CD, aby zapewnić, że wszystkie testy są uruchamiane nawet jeśli niektóre nie powiodą się, dając pełny obraz kompatybilności.

