# Changelog

## [Unreleased]

### Changed

-   Formatted all source code to PSR-2 standards for Blesta
-   Fixed a couple "undefined variable" errors encountered on initial installation
-   Updated the look of the submit buttons
-   Fixed a "Undefined property" error, when there are no domains available in Audit Domains

### Fixed

-   Fixes #55. [6e4ac5f](https://github.com/NETLINK/Blesta-Namesilo/commit/6e4ac5f51aa224fe3df23ab93762d7be6ffb2f07) by [@julianmatz](https://github.com/julianmatz)

## [1.8.5] = 2018-11-19

### Added

-   Automatically remove spaces from beginning/end of domain registrations/transfers

### Changed

-   Hide NS fields for admin transfer orders
-   Fix multi-year registrations during Namesilo promotions
-   Fix exception when editing API info if only one portfolio exists
-   Fix disallow domain privacy on .us domains
-   Fix require EPP code from client for transfers

## [1.8.4] = 2018-05-22

### Added

-   non-200 HTTP return codes parsed as error

### Changed

-   Fix admin-side domain registrations
-   Open namesilo links in new tab
-   Fix undefined property error on DNSSEC page (admin)

## [1.8.3] - 2018-04-13

### Changed

-   Fix client DNSSEC format on small screens

## [1.8.2] - 2018-04-11

### Added

-   DNSSEC management support

### Changed

-   Fix email verification status if NameSilo account has a single email for all domains

## [1.8.1] - 2018-04-10

### Changed

-   Fix configuration fields on order form

## [1.8.0] - 2018-04-09

### Added

-   Proper support for .us and .ca TLDs
-   Input validation for EPP code on transfers
-   Support payment_id for registrations & transfers.  See <https://www.namesilo.com/api_reference.php#registerDomain>
-   Set client's info in WHOIS info for domain transfers
-   Give client ability to toggle WHOIS privacy
-   Set default portfolio for registrations/transfers
-   Domain audit (compare namesilo domain list against active/suspended blesta services)
-   Tool to sync service's renew date with domain expiration date
-   Display domain transfer status on all management pages if its pending transfer
-   Client/admin can see email validation status and trigger resending of validation email
-   Support for using NameSilo's batch API
-   Create/manage registered nameservers

### Changed

-   Renamed "Communication" tab to "Admin Actions"
-   Fixed dynamically loading TLDs from NameSilo API

### Removed

-   Old static TLD list from code

## [Prior Versions]

## [1.5-beta]

### Added

-   .club extension
-   .online extension
-   .pub extension
-   .pro extension

## [1.4-beta]

### Added

-   .cloud extension

## [1.3-beta]

### Added

-   Add Admin Communication tab to send domain-related email notifications.

## [1.2-beta]

### Added

-   Disable Settings tab (registrar lock and EPP code) on a per-client basis by adding a custom client field called "Disable Domain Transfers" and checking it on the Client Edit screen.
