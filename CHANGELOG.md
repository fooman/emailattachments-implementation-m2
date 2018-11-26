# Change Log

## [Unreleased]

## [105.0.0] 2018-11-26
### Changed
- Add compatibility with Magento 2.3.0 and handle upgrade of Zend_Mail, for earlier versions of Magento use
previous versions
Constructor change in Model\EmailEventDispatcher
- explicitly state Zend\Mime dependency

## [104.0.4] 2018-09-28
### Added
- Ability to customise affect the final filename

## [104.0.3] 2018-07-23
### Changed
- Reorganised unit tests

## [104.0.2] 2018-07-15
### Changed
- Code Quality improvement - use class constants

## [104.0.1] 2018-07-10
### Changed
- Fixed integration tests

## [104.0.0] 2018-06-25
### Changed
- Major rewrite - removed all preferences, use plugins on TransportBuilder and TransportFactory instead

## [103.0.1] 2018-03-20
### Changed
- Adjusted tests to provide for Pdf Customiser transforming T&Cs to Pdfs

## [103.0.0] 2018-03-15
### Changed
- Package name renamed to fooman/emailattachments-implementation-m2, installation should be via metapackage fooman/emailattachments-m2
- Increased version number by 100 to differentiate from metapackage
- Moved attachment code into separate class
Constructor change Observer\AbstractObserver
- Attachments are also added to emails sent separately

## [2.1.0] 2017-09-01
### Added
- Support for PHP 7.1
- Support for Magento 2.2.0

## [2.0.8] 2017-06-02
### Fixed
- Make CheckoutAgreements dependency explicit

## [2.0.7] 2017-02-28
### Added
- Ability for integration test to check for attachment name

## [2.0.6] 2017-02-26
### Fixed
- Translations of file names (thanks Manuel)

## [2.0.5] 2016-09-22
### Added
- Add note to "Attach Order as Pdf" that it requires the Print Order Pdf extension

## [2.0.4] 2016-06-15
### Changed
- Widen dependencies in preparation for Magento 2.1.0

## [2.0.3] 2016-04-03
### Fixed
- Add missing configuration setting for attaching T&Cs to shipping email

## [2.0.2] 2016-04-01
### Changed
- Release for Marketplace

## [2.0.1] - 2016-03-25
### Added
- Integration tests now support Pdf Customiser supplied attachments

## [2.0.0] - 2016-01-21
### Changed
- Change project folder structure to src/ and tests/ (not applicable for Marketplace version)

## [1.0.0] - 2015-11-29
### Added
- Initial release for Magento 2
