# PHP Compatibility Tester

Uniwersalny, reużywalny pakiet Composer do testowania kompatybilności bibliotek PHP z różnymi frameworkami (Laravel, Symfony, CodeIgniter, itp.) i wersjami PHP.

## Co robi ta paczka?

**W skrócie**: Ta paczka automatycznie testuje, czy Twoja biblioteka PHP działa z różnymi frameworkami i wersjami PHP.

### Problem, który rozwiązuje

Gdy tworzysz bibliotekę PHP (np. `vendor/my-package`), musisz wiedzieć:
- ✅ Czy działa z Laravel 11 i 12?
- ✅ Czy działa z Symfony 7 i 8?
- ✅ Czy działa na PHP 8.1, 8.2, 8.3, 8.4?
- ✅ Czy nie ma konfliktów zależności?

Ręczne sprawdzanie wszystkich tych kombinacji jest czasochłonne i podatne na błędy.

### Jak to działa (krok po kroku)

1. **Tworzy tymczasowe projekty frameworków**
   - Dla każdego frameworka/wersji tworzy nowy projekt (np. `composer create-project laravel/laravel`)

2. **Instaluje Twoją bibliotekę**
   - W każdym projekcie instaluje Twoją bibliotekę przez Composer

3. **Uruchamia testy**
   - Wykonuje Twoje skrypty testowe (np. sprawdza czy klasy się ładują)

4. **Generuje raport**
   - Tworzy szczegółowy raport (Markdown/JSON/HTML) z wszystkimi wynikami

### Przykładowy wynik

Raport pokazuje:
- ✅ Laravel 11.* + PHP 8.2 - **DZIAŁA**
- ❌ Laravel 12.* + PHP 8.1 - **NIE DZIAŁA** (wymaga PHP 8.2+)
- ✅ Symfony 7.4.* + PHP 8.3 - **DZIAŁA**
- I tak dalej...

### Analogia

Jak testy jednostkowe, ale dla kompatybilności:
- **Testy jednostkowe** = Czy mój kod działa poprawnie?
- **Compatibility Tester** = Czy mój kod działa w różnych środowiskach?

### Dla kogo?

- **Twórcy bibliotek** - Chcesz wiedzieć z czym działa Twoja biblioteka
- **Maintainerzy pakietów** - Musisz wspierać wiele frameworków
- **Zespoły** - Chcesz automatycznie sprawdzać kompatybilność w CI/CD

### W jednym zdaniu

**Automatycznie testuje, czy Twoja biblioteka PHP działa z różnymi frameworkami i wersjami PHP, bez ręcznego sprawdzania każdej kombinacji.**

## Funkcjonalności

- Testowanie kompatybilności z wieloma wersjami PHP (8.1+)
- Wsparcie dla głównych frameworków PHP (Laravel, Symfony, CodeIgniter, Laminas, Yii, CakePHP, Slim, Lumen, Phalcon)
- Konfiguracja w formacie YAML
- Polecenia CLI do łatwego testowania
- Wiele formatów raportów (Markdown, JSON, HTML)
- Integracja z GitHub Actions
- Wsparcie dla niestandardowych skryptów testowych

## Instalacja

Zainstaluj przez Composer:

```bash
composer require --dev lukaszzychal/php-compatibility-tester
```

## Szybki start

1. Zainicjalizuj konfigurację w swoim projekcie:

```bash
vendor/bin/compatibility-tester init
```

To utworzy plik `.compatibility.yml` i skopiuje niezbędne szablony.

2. Edytuj `.compatibility.yml`, aby skonfigurować testy:

```yaml
package_name: "your-vendor/your-package"
php_versions: ['8.1', '8.2', '8.3', '8.4']
frameworks:
  laravel:
    versions: ['11.*', '12.*']
    install_command: 'composer create-project laravel/laravel'
    php_min_version: '8.1'
```

3. Uruchom testy kompatybilności:

```bash
vendor/bin/compatibility-tester test
```

4. Wygeneruj raport:

```bash
vendor/bin/compatibility-tester report --format=markdown --output=report.md
```

## Użycie

### Inicjalizacja konfiguracji

```bash
vendor/bin/compatibility-tester init
```

Tworzy `.compatibility.yml` i kopiuje pliki szablonów do projektu.

### Uruchamianie testów

```bash
# Uruchom wszystkie testy
vendor/bin/compatibility-tester test

# Filtruj po frameworku
vendor/bin/compatibility-tester test --framework=laravel

# Filtruj po wersji frameworka
vendor/bin/compatibility-tester test --framework=laravel --version=11.*

# Filtruj po wersji PHP
vendor/bin/compatibility-tester test --php=8.3
```

### Generowanie raportów

```bash
# Wygeneruj raport Markdown (domyślny)
vendor/bin/compatibility-tester report

# Wygeneruj raport JSON
vendor/bin/compatibility-tester report --format=json --output=report.json

# Wygeneruj raport HTML
vendor/bin/compatibility-tester report --format=html --output=report.html
```

## Konfiguracja

Plik `.compatibility.yml` obsługuje następujące opcje:

- `package_name`: Nazwa Twojego pakietu (np. "vendor/package")
- `php_versions`: Tablica wersji PHP do testowania
- `frameworks`: Konfiguracje frameworków
  - `versions`: Wersje frameworków do testowania
  - `install_command`: Polecenie do utworzenia projektu frameworka
  - `php_min_version`: Minimalna wymagana wersja PHP
  - `php_min_version_X`: Wymagania PHP specyficzne dla wersji
- `test_scripts`: Niestandardowe skrypty testowe do uruchomienia
- `github_actions`: Ustawienia workflow GitHub Actions

Zobacz [Dokumentację konfiguracji](docs/configuration.md) dla szczegółowych informacji.

## Wspierane frameworki

- Laravel (11.x, 12.x)
- Symfony (7.4.x, 8.0.x)
- CodeIgniter (4.x, 5.x)
- Laminas (3.x)
- Yii (2.0.x)
- CakePHP (5.x)
- Slim (4.x, 5.x)
- Lumen (11.x)
- Phalcon (5.x)

## Wymagania

- PHP 8.1 lub wyższa
- Composer
- Komponenty Symfony Console, Process i YAML (instalowane automatycznie)

## Dokumentacja

- [Przewodnik instalacji](docs/installation.md)
- [Referencja konfiguracji](docs/configuration.md)
- [Przewodnik użycia](docs/usage.md)
- [Przykłady](docs/examples/)

## Linki

- **Packagist**: [lukaszzychal/php-compatibility-tester](https://packagist.org/packages/lukaszzychal/php-compatibility-tester)
- **GitHub**: [lukaszzychal/php-compatibility-tester](https://github.com/lukaszzychal/php-compatibility-tester)
- **Issues**: [Zgłoś błąd lub zaproponuj funkcję](https://github.com/lukaszzychal/php-compatibility-tester/issues)
- **Discussions**: [Zadaj pytania i podziel się pomysłami](https://github.com/lukaszzychal/php-compatibility-tester/discussions)

## Wsparcie

Wkłady są mile widziane! Możesz przesłać Pull Request.

- [Otwórz Issue](https://github.com/lukaszzychal/php-compatibility-tester/issues) aby zgłosić błąd lub zaproponować funkcję
- [Rozpocznij Discussion](https://github.com/lukaszzychal/php-compatibility-tester/discussions) aby zadać pytanie lub podzielić się pomysłem
- Prześlij Pull Request aby wnieść kod

## Licencja

Ten pakiet jest oprogramowaniem open source licencjonowanym na [licencji MIT](LICENSE.md).

## Autor

Lukasz Zychal

