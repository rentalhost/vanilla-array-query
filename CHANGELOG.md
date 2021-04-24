# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.2] - 2021-04-24

### Changed

- When a `callable` is used as *value*, the source array will be updated to contains the new key accessible to next queries. The original source array will not be affected.

## [0.1.1] - 2021-04-22

### Added

- Using a `callable` as *value* of a query will returns a transformed output to the *key*. The `callable` itself will receives as argument all elements of that level;

## [0.1.0] - 2020-08-04

### Added

- Initial version;

[0.1.2]: https://github.com/rentalhost/vanilla-array-query/compare/0.1.1..0.1.2

[0.1.1]: https://github.com/rentalhost/vanilla-array-query/compare/0.1.0..0.1.1

[0.1.0]: https://github.com/rentalhost/vanilla-array-query/tree/0.1.0
