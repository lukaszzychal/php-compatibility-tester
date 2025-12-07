# Utworzenie pakietu PHP Compatibility Tester

## Cel

Stworzenie uniwersalnego, reużywalnego pakietu Composer `php-compatibility-tester`, który umożliwi łatwe testowanie kompatybilności bibliotek i aplikacji PHP z różnymi frameworkami (Laravel, Symfony) i wersjami PHP.

## Struktura pakietu

### 1. Podstawowa struktura projektu

```
php-compatibility-tester/
├── composer.json                    # Konfiguracja pakietu
├── README.md                        # Dokumentacja główna
├── LICENSE                          # MIT License
├── CHANGELOG.md                     # Historia zmian
├── .gitignore
├── src/                             # Kod źródłowy
│   ├── CompatibilityTester.php     # Główna klasa testera
│   ├── ConfigLoader.php             # Ładowanie konfiguracji YAML
│   ├── FrameworkTester.php         # Testowanie frameworków
│   ├── ComposerTester.php           # Testowanie Composer
│   ├── ReportGenerator.php         # Generowanie raportów
│   ├── Command/                     # Symfony Console commands
│   │   ├── TestCommand.php
│   │   ├── ReportCommand.php
│   │   └── InitCommand.php
│   └── Exception/                   # Wyjątki
│       ├── CompatibilityException.php
│       └── ConfigurationException.php
├── bin/
│   └── compatibility-tester       # CLI executable
├── templates/                       # Szablony do kopiowania
│   ├── github-actions/
│   │   └── compatibility-tests.yml
│   ├── phpunit/
│   │   ├── FrameworkCompatibilityTest.php
│   │   └── ComposerCompatibilityTest.php
│   ├── scripts/
│   │   └── compatibility-test.sh
│   └── config/
│       └── .compatibility.yml.example
├── tests/                           # Testy pakietu
│   ├── Unit/
│   └── Integration/
└── docs/                            # Dokumentacja
    ├── installation.md
    ├── configuration.md
    ├── usage.md
    └── examples/
```

### 2. Zależności pakietu

**Wymagane:**

- `php: ^8.1`
- `symfony/console: ^6.0|^7.0` - CLI interface
- `symfony/process: ^6.0|^7.0` - Uruchamianie procesów
- `symfony/yaml: ^6.0|^7.0` - Parsowanie YAML

**Opcjonalne (dev):**

- `phpunit/phpunit: ^10.0`
- `phpstan/phpstan: ^1.10`
- `vimeo/psalm: ^6.0`

### 3. Funkcjonalności CLI

#### Komenda `init`

```bash
vendor/bin/compatibility-tester init
```

- Tworzy `.compatibility.yml` z przykładową konfiguracją
- Kopiuje szablony testów do projektu
- Konfiguruje GitHub Actions workflow

#### Komenda `test`

```bash
vendor/bin/compatibility-tester test
vendor/bin/compatibility-tester test --framework=laravel --version=12.*
vendor/bin/compatibility-tester test --php=8.3
```

- Uruchamia testy kompatybilności
- Obsługuje filtrowanie po framework/version/PHP
- Generuje wyniki w czasie rzeczywistym

#### Komenda `report`

```bash
vendor/bin/compatibility-tester report
vendor/bin/compatibility-tester report --format=markdown --output=report.md
vendor/bin/compatibility-tester report --format=json
```

- Generuje raporty z wyników testów
- Obsługuje formaty: markdown, json, html
- Może czytać wyniki z plików lub z pamięci

### 4. Format konfiguracji

Plik `.compatibility.yml`:

```yaml
package_name: "vendor/package-name"
php_versions: ['8.1', '8.2', '8.3', '8.4']
frameworks:
  laravel:
    versions: ['11.*', '12.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'  # Laravel 11
    php_min_version_12: '8.2' # Laravel 12
  symfony:
    versions: ['7.4.*', '8.0.*']
    install_command: 'composer create-project symfony/symfony'
    php_min_version: '8.1'
    php_min_version_8: '8.2' # Symfony 8.0
  codeigniter:
    versions: ['4.*', '5.*']
    install_command: 'composer create-project codeigniter4/appstarter'
    php_min_version: '8.1'
  laminas:
    versions: ['3.*']
    install_command: 'composer create-project laminas/laminas-mvc-skeleton'
    php_min_version: '8.1'
  yii:
    versions: ['2.0.*']
    install_command: 'composer create-project yiisoft/yii2-app-basic'
    php_min_version: '8.0'
  cakephp:
    versions: ['5.*']
    install_command: 'composer create-project cakephp/app'
    php_min_version: '8.2'
  slim:
    versions: ['4.*', '5.*']
    install_command: 'composer create-project slim/slim-skeleton'
    php_min_version: '8.1'
  lumen:
    versions: ['11.*']
    install_command: 'composer create-project laravel/lumen'
    php_min_version: '8.1'
  phalcon:
    versions: ['5.*']
    install_command: 'composer create-project phalcon/mvc'
    php_min_version: '8.1'
    note: 'Requires Phalcon PHP extension'
test_scripts:
  - name: autoloading
    script: 'tests/compatibility/check-autoload.php'
    description: 'Test class autoloading'
  - name: basic_functionality
    script: 'tests/compatibility/check-basic.php'
    description: 'Test basic library functionality'
github_actions:
  schedule: '0 2 * * 1'  # Weekly on Monday
  on_push: true
  paths:
    - 'composer.json'
    - 'composer.lock'
```

### 5. Integracja z istniejącym projektem

Po instalacji pakietu w projekcie TMDB Client:

1. Usunięcie duplikujących się plików (zostają tylko specyficzne testy)
2. Konfiguracja `.compatibility.yml` dla projektu
3. Użycie pakietu jako zależności dev
4. Aktualizacja dokumentacji

## Implementacja

### Faza 1: Przygotowanie struktury pakietu

- Utworzenie nowego repozytorium/katalogu
- Podstawowa struktura katalogów
- `composer.json` z zależnościami
- `.gitignore` i podstawowe pliki

### Faza 2: Core functionality

- `ConfigLoader` - ładowanie i walidacja konfiguracji YAML
- `FrameworkTester` - testowanie kompatybilności z frameworkami
- `ComposerTester` - testowanie rozwiązywania zależności
- `ReportGenerator` - generowanie raportów

### Faza 3: CLI Interface

- Symfony Console commands (init, test, report)
- Obsługa argumentów i opcji
- Kolorowe output i progress bars
- Error handling

### Faza 4: Szablony i integracja

- Szablony GitHub Actions workflow
- Szablony testów PHPUnit
- Szablony skryptów bash
- Przykładowe konfiguracje

### Faza 5: Dokumentacja

- README z quick start
- Dokumentacja konfiguracji
- Przykłady użycia
- Best practices

### Faza 6: Testy i jakość

- Testy jednostkowe pakietu
- Testy integracyjne
- PHPStan/Psalm
- CI/CD dla pakietu

### Faza 7: Migracja z TMDB Client

- Refaktoryzacja istniejącego kodu
- Użycie pakietu w TMDB Client
- Aktualizacja dokumentacji projektu
- Weryfikacja działania

## Pliki do utworzenia

### Core package files

- `composer.json` - konfiguracja pakietu
- `src/CompatibilityTester.php` - główna klasa
- `src/ConfigLoader.php` - loader konfiguracji
- `src/FrameworkTester.php` - tester frameworków
- `src/ComposerTester.php` - tester Composer
- `src/ReportGenerator.php` - generator raportów

### CLI Commands

- `src/Command/InitCommand.php`
- `src/Command/TestCommand.php`
- `src/Command/ReportCommand.php`

### Templates

- `templates/github-actions/compatibility-tests.yml`
- `templates/phpunit/FrameworkCompatibilityTest.php`
- `templates/phpunit/ComposerCompatibilityTest.php`
- `templates/scripts/compatibility-test.sh`
- `templates/config/.compatibility.yml.example`

### Documentation

- `README.md`
- `docs/installation.md`
- `docs/configuration.md`
- `docs/usage.md`
- `docs/examples/` - przykłady użycia

## Użycie w projekcie

Po utworzeniu pakietu, użycie w TMDB Client:

```json
{
  "require-dev": {
    "lukaszzychal/php-compatibility-tester": "^1.0"
  },
  "scripts": {
    "compatibility-test": "compatibility-tester test",
    "compatibility-report": "compatibility-tester report"
  }
}
```
```bash
# Inicjalizacja
vendor/bin/compatibility-tester init

# Testowanie
composer compatibility-test

# Raport
composer compatibility-report
```

## Korzyści

1. **Reużywalność** - jeden pakiet dla wszystkich projektów
2. **Standaryzacja** - spójne podejście w ekosystemie
3. **Łatwa integracja** - przez Composer
4. **Konfigurowalność** - przez YAML
5. **Rozszerzalność** - plugin system
6. **Wsparcie społeczności** - open source na GitHub/Packagist

## Publikacja

- Repozytorium GitHub: `lukaszzychal/php-compatibility-tester`
- Packagist: `lukaszzychal/php-compatibility-tester`
- Wersja początkowa: `1.0.0`
- Licencja: MIT