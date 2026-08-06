# [DD] Bump - Kurzanleitung & Nutzung

## Installation
1. Lade den Inhalt nach `src/addons/cv6/Bump` hoch.
2. Installiere per CLI:
   ```bash
   php cmd.php xf-addon:install cv6/Bump
   ```

## CLI-Kurzanleitung

Führe im Hauptverzeichnis deiner XenForo-Installation den Befehl `cv6:bump` aus, um die Version eines Add-ons zu erhöhen:

```bash
php cmd.php cv6:bump Vendor/Addon [type] [state]
```

### Parameter
- **`Vendor/Addon`**: Die ID des Add-ons (z. B. `cv6/DiceRoller`).
- **`type`** *(optional)*: Bumping-Typ (`major`, `minor`, `patch`, `alpha`, `beta`, `rc`, `stable`).
- **`state`** *(optional)*: Stabilitäts-Stufe falls spezifisch vorgegeben.

### Beispiele
- Patch-Version erhöhen (z. B. 1.0.0 -> 1.0.1):
  ```bash
  php cmd.php cv6:bump Vendor/Addon patch
  ```
- Minor-Version erhöhen (z. B. 1.0.0 -> 1.1.0):
  ```bash
  php cmd.php cv6:bump Vendor/Addon minor
  ```
- Auf Stable setzen:
  ```bash
  php cmd.php cv6:bump Vendor/Addon stable
  ```
- Interaktive Abfrage:
  ```bash
  php cmd.php cv6:bump Vendor/Addon
  ```
