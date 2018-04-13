# Changelog

## [Unreleased]

## [1.8.3] - 2018-04-13
### Changed
- Fix client DNSSEC format on small screens

## [1.8.2] - 2018-04-11
### Added
- DNSSEC management support

### Changed
- Fix email verification status if NameSilo account has a single email for all domains

## [1.8.1] - 2018-04-10
### Changed
- Fix configuration fields on order form

## [1.8.0] - 2018-04-09
### Added
- Proper support for .us and .ca TLDs
- Input validation for EPP code on transfers
- Support payment_id for registrations & transfers.  See https://www.namesilo.com/api_reference.php#registerDomain
- Set client's info in WHOIS info for domain transfers
- Give client ability to toggle WHOIS privacy
- Set default portfolio for registrations/transfers
- Domain audit (compare namesilo domain list against active/suspended blesta services)
- Tool to sync service's renew date with domain expiration date
- Display domain transfer status on all management pages if its pending transfer
- Client/admin can see email validation status and trigger resending of validation email
- Support for using NameSilo's batch API
- Create/manage registered nameservers

### Changed
- Renamed "Communication" tab to "Admin Actions"
- Fixed dynamically loading TLDs from NameSilo API

### Removed
- Old static TLD list from code

## [Prior Versions]
This changelog starts at version 1.8.0 when @neonardo1 started contributions.
Detailed changelogs before this point may be innacurate.

## [1.5-beta]
### Added
- .club extension
- .online extension
- .pub extension
- .pro extension

## [1.4-beta]
### Added
- .cloud extension

## [1.3-beta]
### Added
- Add Admin Communication tab to send domain-related email notifications.

## [1.2-beta]
### Added
- Disable Settings tab (registrar lock and EPP code) on a per-client basis by adding a custom client field called "Disable Domain Transfers" and checking it on the Client Edit screen.