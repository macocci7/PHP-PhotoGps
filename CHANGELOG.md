# Changelog

## 2025/01/04: version updated => 1.8.4

### what's changed

- Upgraded dependencies.
  - intervention/image: 3.7 => 3.10
  - squizlabs/php_codesniffer: 3.7 => 3.11
  - phpstan/phpstan: 1.11 => 2.1
  - phpmd/phpmd: removed.
- PHP8.4 compatibility.
- (DEV) Replaced phpenv with mise.
- (DEV) Removed check by phpmd.
- Renamed `example/` to `examples/`.
- Updated github workflows.
- Updated README.

## 2024/10/02: version updated => 1.8.3

### what's changed

- Fixed: misspelled tag names in the definition.

## 2024/08/15: version updated => 1.8.2

### What's Changed

- Changed: `Helpers\Exif::get()` to use `exif_read_data()` in order to reduce memory usage.

## 2024/06/26: version updated => 1.8.1

### What's Changed

- Updated: dependencies
    - intervention/image: 3.6 => 3.7
    - phpstan/phpstan: 1.10 => 1.11

## 2024/04/20: version updated => 1.8.0

### What's Changed

- Version Updated: Intervention/Image: 3.5 => 3.6
- Changed: `Helper` to `Helpers`
- Updated: examples
- Updated: README

## 2024/04/18: version updated => 1.7.0

### What's Changed

- Version Updated: Intervention/Image: 3.3 => 3.5
- Version Updated: PHPUnit/PHPUnit: 9.6 => 10.5
- Updated: Tests to use `DataProvider` Attribute.
- Removed: `composer.lock` and `.php-version` from git control.
- Updated: `Helpers\Exif::simplifyDegrees()` to use explicit conversion from `float` to `int`.
- Updated: GitHub Workflows.
- Updated: examples
- Updated: README

## 2024/02/06: version updated => 1.6.0

### What's Changed

- Version Updated: Intervention/Image: 2.7 => 3.3
- Deprecated: Supporting PHP 8.0
- Updated: README

## 2024/02/06: version updated => 1.5.6

### Improvement

- Added: `DatestampFormat()`
- Added: `resetDatestampFormat()`
- Added: `TimestampFormat()`
- Added: `resetTimestampFormat()`
- Updated: README

## 2024/02/06: version updated => 1.5.5

### Improvement

- Updated: README

## 2024/02/05: version updated => 1.5.4

### Improvement

- Added: `directionFormat()`
- Added: `resetDirectionFormat()`
- Added: `speedFormat()`
- Added: `resetSpeedFormat()`

## 2024/01/27: version updated => 1.5.3

### Improvement

- Updated: `directionS()` supports `lang('ja')`
- Updated: `TrackS()` supports `lang('ja')`
- Updated: `destBearingS()` supports `lang('ja')`

## 2024/01/14: version updated => 1.5.2

### Improvement

- Updated: example
- Updated: README

## 2024/01/14: version updated => 1.5.1

### Improvement

- Updated: README

## 2024/01/14: version updated => 1.5.0

### Improvement

- Updated: Exif versions prior to Version 3.0 are now supported.
    - Exif Version 2.32 (Released: May 2019)
    - Exif Version 2.31 (Released: July 2016)
    - Exif Version 2.3 (Released: April 2010)
    - Exif Version 2.21 (Released: September 2003)
    - Exif Version 2.2 (Released: April 2002)
    - Exif Version 2.1 (Released: December 1998)

    GPS Tag Definitions are based on PDF files on Websites of [CIPA](https://cipa.jp/e/std/std-sec.html) and [Wikipedia](https://en.wikipedia.org/wiki/Exif).
- Updated: examples.
- Updated: README

## 2024/01/09: version updated => 1.4.1

### Improvement

- Fixed: `Macocci7\PhpPhotoGps\Helpers\Arrow` retunrs `Intervention\Image\Image`
- Fixed: `example/ConfigFormat.php`
- Updated: wrong statements in README corrected.

## 2024/01/08: version updated => 1.4.0

### Added

- Support remote image. (only http:, https:)
- Support other GPS tags. 
  - ImgDirection, Track, DestDirection, Speed, DateStamp, TimeStamp
  - Example: `GPS Attribute Information` added.
- Support Conversion
  - BYTE Data into Array
  - BYTE Data into ASCII
  - RATIONAL Data into Float
  - Strip NULL BYTE
- Support Arrow Image for Direction Display
- Method: `hasGeo()` judges if longitude or latitude exists.
- Method: `hasAltitude()` juges if altitude exists.
- Method: `altitudeS()` returns formatted altitude.
- Method: `direction()` and `directionS()` returns converted GPSImgDirection.
- Method: `speed()` and `sppedS()` returns converted GPSSpeed.
- Method: `destBearing()` and `destBearingS()` returns converted GPSDestBearing.
- Method: `track()` and `trackS()` returns converted GPSTrack
- Method: `datestamp()` and `timestamp()` returns converted GPSDateStamp and GPSTimeStamp.

### Improvement

- Update: `hasGps()` judges if any GPS tag exists.

## 2024/01/02: version updated => 1.3.1

- resetting format enabled, documents updated

## 2024/01/01: version updated => 1.3.0

- format configuration enabled

## 2023/11/11: version updated => 1.2.0

- usages of some methods have been changed
