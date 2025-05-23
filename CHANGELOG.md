# Change Log

## [Unreleased]

## [107.5.2] - 2025-04-10
### Fixed
- Adjust test output for mailpit response 

## [107.5.1] - 2025-04-10
### Fixed
- Rework extension to also support Symfony Mailer

## [107.5.0] - 2025-03-04
### Added
- Support for Magento 2.4.8
- Support for Php 8.4

## [107.4.0] - 2024-03-18
### Added
- Support for Php 8.3
### Changed
- Code style updates for newer Magento Coding Standard

## [107.3.0] - 2023-02-07
### Added
- Support for Php 8.2

## [107.2.1] - 2022-01-30
### Added
- Support for PHP 8.0 and 8.1

## [107.2.0] - 2021-09-08
### Changed
- Switch to Laminas Mime package, minimum Magento version is now 2.3.5

## [107.1.1] - 2020-12-10
### Fixed
- Don't send the same named attachment twice

## [107.1.0] - 2020-07-27
### Added
- Support for PHP 7.4
- Support for Magento 2.4.0
### Fixed
- Mislabeled admin setting

## [107.0.1] - 2020-05-01
### Fixed
- Reworked email identification to strictly cover supported types only

## [107.0.0] - 2020-01-21
### Added
- Ability to attach invoices to the shipping confirmation email
### Changed
- Model\Api\PdfRendererInterface getFileName method signature changed
- Updated comments for latest Magento Coding Standards

## [106.0.0] - 2019-10-04
### Changed
- Adjustments for new email handling in Magento 2.3.3
  Removed Plugin\TransportFactory with most functionality now handled by Plugin\MimeMessageFactory,
  Method signature changes for Model\EmailEventDispatcher
- Use previous releases for earlier versions of Magento
- Removed support for Php 7.0

## [105.1.1] - 2019-08-14
### Changed
- Adjust for changed core behaviour around plain text emails

## [105.1.0] - 2019-06-26
### Added
- AttachmentInterface::getFilename(true) now provides the base64 encoded name of the attachment
- PHPStan to development tools
### Fixed
- Some parameter type issues

## [105.0.8] - 2019-05-10
### Changed
- Adopt latest Magento Coding Standards

## [105.0.7] - 2019-04-19
### Fixed
- MFTF tweak if not run in isolation

## [105.0.6] - 2019-04-11
### Fixed
- Reverse additional return types

## [105.0.5] - 2019-04-09
### Fixed
- Reverse adding return types to maintain 2.2.8 compatibility

## [105.0.4] - 2019-03-27
### Added
- Compatibility with Magento 2.2.8

## [105.0.3] - 2019-03-27
### Added
- Initial MFTF acceptance test

## [105.0.2] - 2018-12-11
### Changed
- Reverse 7.1 features as Magento Marketplace does not yet support it

## [105.0.1] - 2018-11-27
### Changed
- Use newer php features (minimum 7.1)

## [105.0.0] - 2018-11-26
### Changed
- Add compatibility with Magento 2.3.0 and handle upgrade of Zend_Mail, for earlier versions of Magento use
previous versions
Constructor change in Model\EmailEventDispatcher
- explicitly state Zend\Mime dependency

## [104.0.4] - 2018-09-28
### Added
- Ability to customise affect the final filename

## [104.0.3] - 2018-07-23
### Changed
- Reorganised unit tests

## [104.0.2] - 2018-07-15
### Changed
- Code Quality improvement - use class constants

## [104.0.1] - 2018-07-10
### Changed
- Fixed integration tests

## [104.0.0] - 2018-06-25
### Changed
- Major rewrite - removed all preferences, use plugins on TransportBuilder and TransportFactory instead

## [103.0.1] - 2018-03-20
### Changed
- Adjusted tests to provide for Pdf Customiser transforming T&Cs to Pdfs

## [103.0.0] - 2018-03-15
### Changed
- Package name renamed to fooman/emailattachments-implementation-m2, installation should be via metapackage fooman/emailattachments-m2
- Increased version number by 100 to differentiate from metapackage
- Moved attachment code into separate class
Constructor change Observer\AbstractObserver
- Attachments are also added to emails sent separately

## [2.1.0] - 2017-09-01
### Added
- Support for PHP 7.1
- Support for Magento 2.2.0

## [2.0.8] - 2017-06-02
### Fixed
- Make CheckoutAgreements dependency explicit

## [2.0.7] - 2017-02-28
### Added
- Ability for integration test to check for attachment name

## [2.0.6] - 2017-02-26
### Fixed
- Translations of file names (thanks Manuel)

## [2.0.5] - 2016-09-22
### Added
- Add note to "Attach Order as Pdf" that it requires the Print Order Pdf extension

## [2.0.4] - 2016-06-15
### Changed
- Widen dependencies in preparation for Magento 2.1.0

## [2.0.3] - 2016-04-03
### Fixed
- Add missing configuration setting for attaching T&Cs to shipping email

## [2.0.2] - 2016-04-01
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
