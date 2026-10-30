# [DD] Bump
*Diese Dokumentation ist auch verfügbar auf: [English](README.md)*

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg) ![XenForo](https://img.shields.io/badge/XenForo-2.3%2B-orange.svg) ![PHP](https://img.shields.io/badge/PHP-8.2%2B-qt.svg)

**[DD] Bump** ist ein Entwickler-CLI-Tool für XenForo 2.3 zur automatischen semantischen Versionserhöhung von Add-ons gemäß den offiziellen XenForo-Standards.

## Funktionen
* **Semantische Versionsberechnung**: Berechnet und erhöht Major-, Minor-, Patch-, Alpha-, Beta-, RC- und Stable-Versions-IDs exakt nach XenForo-Standard.
* **Synchrone Aktualisierung**: Aktualisiert `addon.json` und die Datenbanktabelle `xf_addon` zeitgleich in einem einzigen Schritt.
* **Interaktiver Modus**: Fragt den Bumping-Typ interaktiv ab, falls keine Argumente übergeben werden.
* **Sicherheitsoptionen**: Bietet `--dry-run` zur Vorschau ohne Änderungen sowie `--force` zum Überspringen von Validierungsprüfungen.

## Voraussetzungen
* XenForo 2.3.0+, PHP 8.2.0+

## Nutzung
Führe im Hauptverzeichnis deiner XenForo-Installation den Befehl `dd:bump` aus:

```bash
php cmd.php dd:bump Vendor/Addon [type] [state]
```

### Parameter & Optionen
- **`Vendor/Addon`**: Die ID des Add-ons (z. B. `cv6/DiceRoller`).
- **`type`** *(optional)*: Bumping-Typ (`major`, `minor`, `patch`, `alpha`, `beta`, `rc`, `stable`).
- **`state`** *(optional)*: Optionale Stabilitätsstufe.
- **`--dry-run`**: Zeigt Änderungen an, ohne Dateien oder Datenbank zu verändern.
- **`--force`** (`-f`): Überspringt Validierungsprüfungen der Versions-ID.

### Beispiele
- Patch-Version erhöhen (z. B. 1.0.0 -> 1.0.1):
  ```bash
  php cmd.php dd:bump Vendor/Addon patch
  ```
- Minor-Version erhöhen (z. B. 1.0.0 -> 1.1.0):
  ```bash
  php cmd.php dd:bump Vendor/Addon minor
  ```
- Auf Stable setzen:
  ```bash
  php cmd.php dd:bump Vendor/Addon stable
  ```
- Interaktive Abfrage:
  ```bash
  php cmd.php dd:bump Vendor/Addon
  ```
