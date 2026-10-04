# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2026-10-05

### Added

- Added the Amberworld sources for AMBtool, AMBlib, and AMgfx together with reproducible CMake and shell builds.
- Added native AMBtool and AMgfx binaries for Windows x64, Linux x64, and Linux ARM64.
- Added native-library tests on Windows x64, Linux x64, and Linux ARM64 and expanded the PHP CI matrix to the same platforms.

### Changed

- Reorganized command-line assets by tool under `assets/cli` and kept Ambermap with its DLLs and game data.
- Replaced Wine-based execution with direct native executable selection and synchronous process execution.

### Removed

- Removed the Wine process integration.

## [1.1.21] - 2026-04-02

### Changed

- Restored PHP 8.2 support and refreshed the Farah, Savegame, PHPUnit, and Farah Testing requirements.

## [1.1.20] - 2026-04-02

### Added

- Added map groupings and expanded spell and place data used by the editor and viewers.

### Changed

- Raised the minimum PHP version to 8.3 and updated Farah and Savegame dependencies.

### Fixed

- Corrected the Morag inn data and improved the display of unused character statistics.

## [1.1.19] - 2026-03-14

### Added

- Added the character creation and editing workflow, including attributes, skills, spells, equipment, portraits, shops, chests, and savegame operations.
- Added item, loot, portrait, spell, and character viewers together with popup, tab, picker, and Vue-based UI components.
- Added `AmberExecutableBuilder` and expanded executable, editor-data, and savegame test coverage.
- Added support for PHP 8.5.

### Changed

- Reorganized editor styles, scripts, templates, and fonts and removed obsolete generated release files.

### Fixed

- Corrected editor caching, archive paths, graphics, fonts, layouts, dictionaries, and game-data edge cases.

## [1.1.18] - 2026-01-19

### Added

- Added Pyrdacor Ambermoon data variants, repository-aware datasets, dictionary generation, and new viewers for items, portraits, classes, and NPCs.
- Added Amiga executable parsing, depacking, building, and extraction support with typed data-access implementations.

### Changed

- Raised the minimum PHP version to 8.0.
- Reorganized game sources, templates, dictionaries, and package assets and retired obsolete non-Thalion and legacy web resources.

### Fixed

- Corrected dictionary, URL, stylesheet, executable, and AM2 data handling.

## [1.1.17] - 2025-12-31

### Changed

- Improved stylesheet generation performance.

### Fixed

- Corrected generated graphics, CSS, character sizing, casting, and save-library output.

## [1.1.16] - 2025-12-27

### Added

- Added dataset parameter filtering and support for the updated Savegame APIs.

### Changed

- Updated Core, Savegame, and Farah Testing dependencies and increased the Composer process timeout for the test suite.
- Strengthened PHP types, sealed implementation classes, and normalized XML and XSL resources.

### Fixed

- Retried broken image generation and corrected nullable parameters, infoset types, stylesheet URIs, and site fragments.

## [1.1.15] - 2025-12-01

### Changed

- Updated Farah and Savegame dependencies and adopted the shared Farah Testing package.

### Fixed

- Corrected FAQ links, namespaces, directory creation, and test integration.

## [1.1.14] - 2025-10-03

### Changed

- Updated Farah and moved temporary Wine state into the application cache directory.

### Fixed

- Corrected CI server and temporary-directory handling.

## [1.1.13] - 2025-09-19

### Changed

- Updated the package for Farah 1.19 and normalized source formatting.

### Fixed

- Corrected generated asset URLs.

## [1.1.12] - 2025-09-10

### Added

- Added event, monster, and background data used by the Ambermoon views.
- Added persistent Wine process support for the bundled Windows tools on Linux.

### Changed

- Centralized Windows executable handling and updated the Farah dependency.

### Fixed

- Corrected process exit-code, environment, and exception handling.

## [1.1.11] - 2025-09-04

### Changed

- Replaced the development Savegame dependency with the stable 1.0 release line.

## [1.1.10] - 2025-09-03

### Added

- Added automated tests for AMBtool and AMgfx and Wine support for running the bundled Windows executables on Linux.
- Added the package bootstrap and required the GD and Imagick extensions used by asset generation.

### Changed

- Updated the package to PHP 7.4 or later, PHPUnit 9.6, and current Core, Farah, and Savegame APIs.
- Switched command execution to Symfony Process and modernized CI and documentation deployment.

### Fixed

- Corrected executable invocation, asset generation, and compatibility with updated dependencies.

## [1.1.9] - 2025-07-04

### Changed

- Moved generated API documentation to GitHub Pages and removed generated documentation from the repository.

## [1.1.8] - 2024-11-11

### Fixed

- Corrected alert rendering in the description stylesheet.

## [1.1.7] - 2024-11-11

### Fixed

- Corrected Webpack asset declarations in the module manifest.

## [1.1.6] - 2024-10-05

### Changed

- Added environment-based project configuration and refreshed PHPUnit and PHPDoc configuration.

## [1.1.5] - 2024-09-29

### Changed

- Changed the project license from WTFPL to MIT.

## [1.1.4] - 2024-09-29

### Changed

- Removed the Composer classmap-authoritative override and refreshed project metadata.

## [1.1.3] - 2024-09-23

### Changed

- Moved development metadata from the `develop` branch to `main` and normalized repository files.

## [1.1.2] - 2024-04-01

### Changed

- Removed obsolete Eclipse build-path configuration and refreshed generated API documentation and dependencies.

## [1.1.1] - 2024-04-01

### Changed

- Refreshed development configuration, dependency locks, and PHPUnit settings.

## [1.1.0] - 2022-01-21

### Added

- Added GitHub Actions CI, generated API documentation, screenshots, and expanded Ambermoon dictionaries, maps, spells, classes, and editor templates.

### Changed

- Updated the module to Farah 1.1, modernized namespaces and package layout, and refreshed Core, Farah, Savegame, and PHPUnit integration.
- Made Ambermoon dictionaries schema-compliant and added null infoset and template handling.

### Fixed

- Corrected bootstrap warnings and empty-document handling.

## [1.0.0] - 2018-08-10

Initial release of the Farah module with Ambermoon archive, graphics, infoset, editor, and savegame processing.

[unreleased]: https://github.com/Faulo/slothsoft-amber/compare/1.2.0...HEAD
[1.2.0]: https://github.com/Faulo/slothsoft-amber/compare/1.1.21...1.2.0
[1.1.21]: https://github.com/Faulo/slothsoft-amber/compare/1.1.20...1.1.21
[1.1.20]: https://github.com/Faulo/slothsoft-amber/compare/1.1.19...1.1.20
[1.1.19]: https://github.com/Faulo/slothsoft-amber/compare/1.1.18...1.1.19
[1.1.18]: https://github.com/Faulo/slothsoft-amber/compare/1.1.17...1.1.18
[1.1.17]: https://github.com/Faulo/slothsoft-amber/compare/1.1.16...1.1.17
[1.1.16]: https://github.com/Faulo/slothsoft-amber/compare/1.1.15...1.1.16
[1.1.15]: https://github.com/Faulo/slothsoft-amber/compare/1.1.14...1.1.15
[1.1.14]: https://github.com/Faulo/slothsoft-amber/compare/1.1.13...1.1.14
[1.1.13]: https://github.com/Faulo/slothsoft-amber/compare/1.1.12...1.1.13
[1.1.12]: https://github.com/Faulo/slothsoft-amber/compare/1.1.11...1.1.12
[1.1.11]: https://github.com/Faulo/slothsoft-amber/compare/1.1.10...1.1.11
[1.1.10]: https://github.com/Faulo/slothsoft-amber/compare/1.1.9...1.1.10
[1.1.9]: https://github.com/Faulo/slothsoft-amber/compare/1.1.8...1.1.9
[1.1.8]: https://github.com/Faulo/slothsoft-amber/compare/1.1.7...1.1.8
[1.1.7]: https://github.com/Faulo/slothsoft-amber/compare/1.1.6...1.1.7
[1.1.6]: https://github.com/Faulo/slothsoft-amber/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/Faulo/slothsoft-amber/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/Faulo/slothsoft-amber/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/Faulo/slothsoft-amber/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/Faulo/slothsoft-amber/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/Faulo/slothsoft-amber/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/Faulo/slothsoft-amber/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/Faulo/slothsoft-amber/releases/tag/1.0.0
