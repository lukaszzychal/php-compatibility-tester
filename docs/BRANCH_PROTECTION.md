# Branch Protection Rules - Konfiguracja

## Problem: Merge przed zakończeniem pipeline

Jeśli zmergowałeś PR zanim pipeline się zakończył, możesz skonfigurować Branch Protection Rules, które wymagają ukończenia wszystkich status checks przed mergem.

## Konfiguracja Branch Protection Rules

### Krok 1: Przejdź do ustawień repozytorium

1. Otwórz repozytorium na GitHub
2. Kliknij **Settings** → **Branches**
3. Kliknij **Add rule** lub edytuj istniejącą regułę dla `main`

### Krok 2: Skonfiguruj regułę

**Branch name pattern:** `main` (lub `*` dla wszystkich branchy)

**Zaznacz następujące opcje:**

✅ **Require a pull request before merging**
   - Require approvals: `1` (lub więcej)
   - Dismiss stale pull request approvals when new commits are pushed: ✅

✅ **Require status checks to pass before merging**
   - Require branches to be up to date before merging: ✅
   - **Status checks that are required:**
     - `PHP 8.1 on ubuntu-latest`
     - `PHP 8.2 on ubuntu-latest`
     - `PHP 8.3 on ubuntu-latest`
     - `PHP 8.4 on ubuntu-latest`
     - `Lint`
     - `Security Scan`
     - (dodaj wszystkie joby z workflow)

✅ **Require conversation resolution before merging**

✅ **Do not allow bypassing the above settings** (opcjonalne, dla większego bezpieczeństwa)

### Krok 3: Zapisz

Kliknij **Create** lub **Save changes**

## Automatyczne wykrywanie status checks

GitHub automatycznie wykrywa status checks z workflow. Możesz:

1. Utworzyć PR
2. Poczekać aż workflow się uruchomi
3. W ustawieniach Branch Protection zobaczysz listę dostępnych status checks
4. Zaznacz te, które mają być wymagane

## Przykładowa konfiguracja dla tego projektu

Dla `php-compatibility-tester`, wymagane status checks to:

```
✅ PHP 8.1 on ubuntu-latest
✅ PHP 8.2 on ubuntu-latest
✅ PHP 8.3 on ubuntu-latest
✅ PHP 8.4 on ubuntu-latest
✅ Lint
✅ Security Scan
```

## Workflow z wymaganymi status checks

Aby status checks były widoczne w Branch Protection, muszą być zdefiniowane w workflow:

```yaml
jobs:
  tests:
    name: PHP ${{ matrix.php-version }} on ${{ matrix.os }}
    runs-on: ${{ matrix.os }}
    # ... steps ...
    
  lint:
    name: Lint
    runs-on: ubuntu-latest
    # ... steps ...
    
  security:
    name: Security Scan
    runs-on: ubuntu-latest
    # ... steps ...
```

## Sprawdzanie status checks w PR

Po skonfigurowaniu Branch Protection Rules:

1. Utwórz nowy PR
2. Zobaczysz sekcję **"All checks have passed"** lub **"Some checks are still running"**
3. Przycisk **"Merge pull request"** będzie zablokowany dopóki wszystkie checks nie przejdą
4. Zobaczysz komunikat: *"Merging is blocked: The base branch requires all commits to be signed"* lub podobny

## Troubleshooting

### Problem: Status checks nie są widoczne

**Rozwiązanie:**
- Upewnij się, że workflow jest uruchomiony dla PR
- Sprawdź czy joby mają unikalne nazwy
- Poczekaj aż workflow się zakończy po raz pierwszy

### Problem: Nie mogę zmergować nawet po przejściu wszystkich checks

**Rozwiązanie:**
- Sprawdź czy wszystkie wymagane status checks są zaznaczone
- Upewnij się, że branch jest "up to date" (wymaga rebase/merge z main)
- Sprawdź czy nie ma innych wymagań (approvals, conversation resolution)

### Problem: Chcę zmergować w trybie awaryjnym

**Rozwiązanie:**
- Jeśli nie zaznaczyłeś "Do not allow bypassing", możesz użyć "Merge without waiting for requirements to be met" (tylko dla maintainerów)
- Lub tymczasowo wyłącz Branch Protection Rules

## Best Practices

1. **Zawsze wymagaj status checks** dla głównych branchy (main, master, develop)
2. **Wymagaj approvals** dla ważnych zmian
3. **Włącz "Require branches to be up to date"** aby uniknąć konfliktów
4. **Nie pozwalaj na bypass** w produkcji (chyba że absolutnie konieczne)

## Linki

- [GitHub Docs: About protected branches](https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches)
- [GitHub Docs: Requiring status checks](https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches#require-status-checks-before-merging)

