# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

### [unreleased]
### Fixed
- bug where parse_url can return null for query and fragment components

### Changed
- removed unnecessary  PHPDocs
- use anonymous class in test instead of additional class in the same file 

## [1.1.0] - 2019-03-08
### Added
- CHANGELOG.md
- strict type declarations to all classes (including tests)
- type hinting for method parameters and return types
- require `ext-mbstring` in composer.json

## [1.0.0] - 2019-02-11
### Changed
- Bump PHPUnit to ^7.5
- Use PHP 7.1, 7.2 and 7.3 in Travis tests

### Removed
- Remove depricated Arr:getValue()

## [0.4.1] - 2019-02-11
### Changed
- Mark Arr::getValue() as deprecated (PHPDocs) since it will be removed later

## [0.4.0] - 2017-02-16
### Removed
- Removed validator dependency (moved to its own package).

## [0.3.4] - 2017-01-22
### Fixed
- Fix minor typo and formatting.

## [0.3.3] - 2016-12-12
### Added
- Add check for scalar values (#4)
- Added is_scalar check against - for example - involuntary array to string conversions.

## [0.3.2] - 2016-10-24
### Fixed
- Correct some rules no value behavior.

## [0.3.1] - 2016-10-24
### Changed
- Handle invalid value types.

## [0.3.0] - 2016-09-02
### Added
- New getValue() method. 
