# [DD] Bump
*This documentation is also available in: [Deutsch](README.de.md)*

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg) ![XenForo](https://img.shields.io/badge/XenForo-2.3%2B-orange.svg) ![PHP](https://img.shields.io/badge/PHP-8.2%2B-qt.svg)

**[DD] Bump** is a developer CLI tool for XenForo 2.3 that automates semantic version bumping for add-ons according to official XenForo standards.

## Features
* **Semantic Versioning**: Calculates and increments Major, Minor, Patch, Alpha, Beta, RC, and Stable version IDs.
* **Synchronized Updates**: Simultaneously updates `addon.json` and the `xf_addon` database table in a single step.
* **Interactive Console**: Prompts for bump type interactively when executed without parameters.
* **Safety Controls**: Includes `--dry-run` to preview changes and `--force` to bypass validation if needed.

## Requirements
* XenForo 2.3.0+, PHP 8.2.0+

## Usage
Execute the `dd:bump` command in your XenForo root directory:

```bash
php cmd.php dd:bump Vendor/Addon [type] [state]
```

### Parameters & Options
- **`Vendor/Addon`**: The ID of the add-on to bump (e.g. `cv6/DiceRoller`).
- **`type`** *(optional)*: Bump type (`major`, `minor`, `patch`, `alpha`, `beta`, `rc`, `stable`).
- **`state`** *(optional)*: Specific state override if setting release candidate or stability levels.
- **`--dry-run`**: Preview version changes without modifying files or database.
- **`--force`** (`-f`): Skip validation of version ID.

### Examples
- Bump patch version (e.g. 1.0.0 -> 1.0.1):
  ```bash
  php cmd.php dd:bump Vendor/Addon patch
  ```
- Bump minor version (e.g. 1.0.0 -> 1.1.0):
  ```bash
  php cmd.php dd:bump Vendor/Addon minor
  ```
- Change stability to stable:
  ```bash
  php cmd.php dd:bump Vendor/Addon stable
  ```
- Interactive prompt:
  ```bash
  php cmd.php dd:bump Vendor/Addon
  ```
