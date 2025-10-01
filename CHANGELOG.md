# Changelog

All notable changes to `nmcli-php` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-01

### Added
- Initial release of nmcli-php Composer package
- Complete object-oriented wrapper for NetworkManager's nmcli command
- Connection management: list, show, add, modify, clone, delete connections
- Device management: list devices, connect/disconnect, show device details  
- WiFi operations: list networks, connect to WiFi, create hotspots
- Import/export functionality for connection configurations
- Custom NmcliException for better error handling
- Flexible sudo support with configurable usage
- PSR-4 autoloading compliance
- Comprehensive test suite with PHPUnit
- Full documentation and usage examples
- MIT license

### Features
- `getConnections()` - Retrieve all network connections as structured objects
- `getDevices()` - List all network devices with details
- Connection lifecycle management (up, down, add, modify, clone, delete)
- WiFi network scanning and connection management
- Hotspot creation capabilities
- VPN and connection import/export functionality
- Structured data output instead of raw command-line responses
- Proper input sanitization and security measures
- Support for both sudo and non-sudo execution modes

### Security
- All user inputs properly escaped using `escapeshellarg()`
- Safe command execution through PHP's `exec()` function
- Configurable privilege escalation with sudo support