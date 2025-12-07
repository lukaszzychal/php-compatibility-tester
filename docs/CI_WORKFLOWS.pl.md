# CI/CD Workflows Documentation

Ten dokument opisuje różnice między plikami CI/CD w projekcie.

## Pliki Workflow

### 1. `ci.yml` - Główny Pipeline CI/CD

**Cel:** Podstawowe testy, jakość kodu i bezpieczeństwo

**Sprawdza:**
- ✅ Testy PHPUnit (8.1, 8.2, 8.3, 8.4)
- ✅ PHPStan (analiza statyczna)
- ✅ PHP-CS-Fixer (sprawdzanie stylu kodu)
- ✅ Walidacja `composer.json`
- ✅ Sprawdzanie składni PHP
- ✅ Skanowanie podatności (Symfony Security Checker)
- ✅ Wykrywanie sekretów (TruffleHog)

**Środowisko:** Natywny PHP na GitHub Actions

**Kiedy się uruchamia:**
- Push do `main` lub `develop`
- Pull Request do `main` lub `develop`

---

### 2. `docker-test.yml` - Testy w Docker

**Cel:** Weryfikacja działania w izolowanych kontenerach Docker

**Sprawdza:**
- ✅ Budowanie obrazu Docker dla każdej wersji PHP
- ✅ Uruchamianie testów PHPUnit w kontenerze
- ✅ Uruchamianie smoke tests w kontenerze
- ✅ Weryfikacja, że wszystkie zależności są poprawnie zainstalowane

**Środowisko:** Docker containers (izolacja, symulacja produkcji)

**Kiedy się uruchamia:**
- Push do `main` lub `develop`
- Pull Request do `main` lub `develop`
- Ręcznie (`workflow_dispatch`)

**Wersje PHP:** 8.1, 8.2, 8.3, 8.4

---

### 3. `self-test.yml` - Testy Integracyjne z Frameworkami

**Cel:** Weryfikacja działania pakietu z rzeczywistymi frameworkami

**Sprawdza:**
- ✅ Instalacja pakietu w projektach frameworków
- ✅ Kompatybilność z Laravel 11.* (LTS) i 12.* (Latest stable)
- ✅ Kompatybilność z Symfony 7.4.* (LTS) i 8.0.* (Latest stable)
- ✅ Kompatybilność z Slim 4.* (LTS) i 5.* (Latest stable)
- ✅ Kompatybilność z CodeIgniter 5.* (Latest stable)
- ✅ Kompatybilność z CakePHP 5.* (Latest stable)
- ✅ Generowanie raportów kompatybilności
- ✅ Testy autoloadingu w frameworkach

**Środowisko:** Natywny PHP na GitHub Actions

**Kiedy się uruchamia:**
- Push do `main` lub `develop`
- Pull Request do `main` lub `develop`
- Miesięcznie 1. dnia miesiąca o 2:00 (cron)

**Matrix:**
- PHP: 8.1, 8.2, 8.3, 8.4
- Frameworks: Laravel (LTS + Latest), Symfony (LTS + Latest), Slim (LTS + Latest), CodeIgniter, CakePHP

**Uwaga:** Testy mogą trwać długo (timeout: 30 minut), więc używają `continue-on-error: true`

---

## Porównanie

| Cecha | `ci.yml` | `docker-test.yml` | `self-test.yml` |
|-------|----------|-------------------|-----------------|
| **Typ testów** | Jednostkowe + jakość | Izolacja Docker | Integracyjne |
| **Frameworki** | ❌ | ❌ | ✅ (Laravel, Symfony, Slim, CodeIgniter, CakePHP) |
| **Docker** | ❌ | ✅ | ❌ |
| **PHPStan** | ✅ | ❌ | ❌ |
| **Security Scan** | ✅ | ❌ | ❌ |
| **Czas wykonania** | ~5-10 min | ~5-10 min | ~20-30 min |
| **Częstotliwość** | Każdy push/PR | Każdy push/PR | Push/PR + monthly |

---

## Kiedy używać którego workflow?

- **`ci.yml`**: Podstawowe testy przy każdej zmianie kodu
- **`docker-test.yml`**: Weryfikacja działania w środowisku produkcyjnym (Docker)
- **`self-test.yml`**: Testy end-to-end z rzeczywistymi frameworkami (dłuższe, rzadziej)

---

## Status Checks

Wszystkie trzy workflow są wymagane przed mergowaniem PR (jeśli są skonfigurowane jako required status checks w ustawieniach branch protection).

