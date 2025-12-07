# Framework Testing Status

## Konfigurowalne vs. Przetestowane Frameworki

**WAŻNE**: Kod źródłowy jest **generyczny** - nie ma hardcoded nazw frameworków. Wszystkie frameworki są **konfigurowalne** (można je dodać do `.compatibility.yml`), ale tylko 3 są **przetestowane** w CI/CD.

### Frameworki KONFIGUROWALNE (można skonfigurować w `.compatibility.yml`)

Pakiet obsługuje konfigurację dla następujących frameworków:

1. **Laravel** - `laravel/laravel`
2. **Symfony** - `symfony/symfony`
3. **CodeIgniter** - `codeigniter4/appstarter`
4. **Laminas** - `laminas/laminas-mvc-skeleton`
5. **Yii** - `yiisoft/yii2-app-basic`
6. **CakePHP** - `cakephp/app`
7. **Slim** - `slim/slim-skeleton`
8. **Lumen** - `laravel/lumen`
9. **Phalcon** - `phalcon/mvc`

### Frameworki RZECZYWIŚCIE TESTOWANE w CI/CD

**Lokalizacja**: `.github/workflows/self-test.yml`

Aktualnie w CI/CD testowane są tylko **3 frameworki**:

1. **Laravel** (wersja 11.*)
2. **Symfony** (wersja 7.4.*)
3. **Slim** (wersja 4.*)

**Dlaczego tylko 3?**

- Testowanie wszystkich frameworków zajmuje dużo czasu
- Wymaga więcej zasobów CI/CD
- Niektóre frameworki wymagają dodatkowych zależności (np. Phalcon wymaga rozszerzenia PHP)

### Jak dodać więcej frameworków do testowania?

Edytuj `.github/workflows/self-test.yml`:

```yaml
matrix:
  framework: [laravel, symfony, slim, codeigniter, cakephp]
  include:
    - framework: laravel
      version: '11.*'
    - framework: symfony
      version: '7.4.*'
    - framework: slim
      version: '4.*'
    - framework: codeigniter
      version: '4.*'
    - framework: cakephp
      version: '5.*'
```

## Wersje PHP

### Aktualnie testowane wersje PHP

**Lokalizacja**: `.github/workflows/self-test.yml` (linia 18)

- PHP 8.1
- PHP 8.2
- PHP 8.3

**Uwaga**: W `docker-test.yml` dodano już PHP 8.4 i 8.5, ale `self-test.yml` wymaga aktualizacji.

### Najnowsze stabilne wersje PHP (2024/2025)

- PHP 8.1 (Long Term Support - wsparcie do 2025)
- PHP 8.2 (Long Term Support - wsparcie do 2026)
- PHP 8.3 (Active Support)
- PHP 8.4 (Active Support - wydana w listopadzie 2024)
- PHP 8.5 (w planach na 2025)

## Rekomendacje

1. **Dla produkcji**: Użyj PHP 8.2 lub 8.3 (LTS)
2. **Dla testowania**: Dodaj PHP 8.4 do testów CI/CD
3. **Dla frameworków**: Rozważ dodanie więcej frameworków do `self-test.yml` jeśli masz wystarczające zasoby CI/CD

## Aktualizacja wersji

Aby zaktualizować wersje PHP we wszystkich miejscach:

1. `.github/workflows/self-test.yml` - matrix php-version
2. `.github/workflows/ci.yml` - matrix php-version
3. `.github/workflows/docker-test.yml` - matrix php-version (już zaktualizowane)
4. `templates/config/.compatibility.yml.example` - php_versions
5. `src/Command/InitCommand.php` - domyślne php_versions
6. Dokumentacja w `docs/`

