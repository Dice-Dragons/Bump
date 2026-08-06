# [DD] Bump - Quick Start & Usage Guide

## Installation
1. Upload contents to `src/addons/cv6/Bump`.
2. Install via CLI:
   ```bash
   php cmd.php xf-addon:install cv6/Bump
   ```

## CLI Usage Guide

To bump an add-on version, execute the `cv6:bump` command in your XenForo root directory:

```bash
php cmd.php cv6:bump Vendor/Addon [type] [state]
```

### Parameters
- **`Vendor/Addon`**: The ID of the add-on to bump (e.g. `cv6/DiceRoller`).
- **`type`** *(optional)*: Bump type (`major`, `minor`, `patch`, `alpha`, `beta`, `rc`, `stable`).
- **`state`** *(optional)*: Specific state override if checking release candidate/stability levels.

### Usage Examples
- Bump patch version (e.g., 1.0.0 -> 1.0.1):
  ```bash
  php cmd.php cv6:bump Vendor/Addon patch
  ```
- Bump minor version (e.g., 1.0.0 -> 1.1.0):
  ```bash
  php cmd.php cv6:bump Vendor/Addon minor
  ```
- Change stability to stable:
  ```bash
  php cmd.php cv6:bump Vendor/Addon stable
  ```
- Interactive prompt:
  ```bash
  php cmd.php cv6:bump Vendor/Addon
  ```
