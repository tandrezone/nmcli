# nmcli-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tandrezone/nmcli-php.svg?style=flat-square)](https://packagist.org/packages/tandrezone/nmcli-php)
[![Total Downloads](https://img.shields.io/packagist/dt/tandrezone/nmcli-php.svg?style=flat-square)](https://packagist.org/packages/tandrezone/nmcli-php)
[![License](https://img.shields.io/packagist/l/tandrezone/nmcli-php.svg?style=flat-square)](https://packagist.org/packages/tandrezone/nmcli-php)

A comprehensive PHP wrapper for NetworkManager's `nmcli` command-line tool. This library provides an object-oriented interface to manage network connections, devices, and WiFi operations on Linux systems.

## Features

- **Connection Management**: List, show, add, modify, clone, delete connections
- **Device Management**: List devices, connect/disconnect, show device details
- **WiFi Operations**: List networks, connect to WiFi, create hotspots
- **Import/Export**: Import and export connection configurations
- **Object-Oriented**: Returns structured arrays instead of raw command output
- **Error Handling**: Custom exceptions for better error management
- **Flexible sudo usage**: Optional sudo support for different use cases
- **PSR-4 Autoloading**: Modern Composer package structure

## Installation

You can install the package via Composer:

```bash
composer require tandrezone/nmcli-php
```

## Requirements

- PHP 7.4 or higher
- NetworkManager with `nmcli` command available
- Linux system with appropriate permissions for network operations
- Optional: `sudo` access for system-level network changes

## Usage

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use Tandrezone\NmcliPhp\Nmcli;
use Tandrezone\NmcliPhp\NmcliException;

// Create nmcli instance (uses sudo by default)
$nmcli = new Nmcli();

// Or create without sudo
$nmcli = new Nmcli(false);

try {
    // Get all connections as objects
    $connections = $nmcli->getConnections();
    foreach ($connections as $connection) {
        echo $connection['NAME'] . " - " . $connection['STATE'] . "\n";
    }

    // Get all devices as objects
    $devices = $nmcli->getDevices();
    foreach ($devices as $device) {
        echo $device['DEVICE'] . " (" . $device['TYPE'] . ") - " . $device['STATE'] . "\n";
    }
} catch (NmcliException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Connection Management

```php
// Show connection details
$details = $nmcli->show('MyConnection');

// Bring connection up/down
$nmcli->up('MyConnection');
$nmcli->down('MyConnection');

// Add new WiFi connection
$success = $nmcli->add('wifi', 'MyWiFi', [
    'ifname' => 'wlan0',
    'ssid' => 'NetworkName',
    'password' => 'password123'
]);

// Modify connection settings
$nmcli->modify('MyConnection', [
    'ipv4.method' => 'manual',
    'ipv4.addresses' => '192.168.1.100/24'
]);

// Clone connection
$nmcli->clone('MyConnection', 'MyConnection-Backup');

// Delete connection
$nmcli->delete('MyConnection-Backup');
```

### WiFi Operations

```php
// List available WiFi networks
$networks = $nmcli->getWifiNetworks();
foreach ($networks as $network) {
    echo $network['SSID'] . " - Signal: " . $network['SIGNAL'] . "\n";
}

// Connect to WiFi network
$success = $nmcli->connectWifi('NetworkSSID', 'password');

// Create WiFi hotspot
$success = $nmcli->createHotspot('MyHotspot', 'hotspotpassword');
```

### Device Management

```php
// Get device details
$deviceDetails = $nmcli->getDeviceDetails('wlan0');

// Connect/disconnect device
$nmcli->connectDevice('wlan0');
$nmcli->disconnectDevice('wlan0');
```

### Import/Export Operations

```php
// Export connection to file
$nmcli->export('MyConnection', '/path/to/backup.nmconnection');

// Import VPN configuration
$nmcli->import('openvpn', '/path/to/config.ovpn');

// Load connection from file
$nmcli->load('/path/to/connection.nmconnection');

// Reload all connections
$nmcli->reload();
```

### Interactive Commands

Some operations return command strings for manual execution in terminal:

```php
// Get interactive edit command
$editCommand = $nmcli->edit('MyConnection');
echo $editCommand; // "sudo nmcli con edit MyConnection"

// Get monitoring command
$monitorCommand = $nmcli->monitor();
echo $monitorCommand; // "sudo nmcli con monitor"
```

### Error Handling

The library uses custom exceptions for better error handling:

```php
try {
    $nmcli->up('NonExistentConnection');
} catch (NmcliException $e) {
    echo "nmcli error: " . $e->getMessage();
} catch (Exception $e) {
    echo "General error: " . $e->getMessage();
}
```

### Sudo Configuration

You can control sudo usage:

```php
$nmcli = new Nmcli(true);  // Use sudo (default)
$nmcli = new Nmcli(false); // Don't use sudo

// Change sudo usage after creation
$nmcli->setUseSudo(false);
$usingSudo = $nmcli->isUsingSudo(); // returns false
```

## API Reference

### Connection Methods
- `getConnections()` - Get all connections as objects
- `show($connection = null)` - Show connection details
- `up($connection)` - Bring connection up
- `down($connection)` - Bring connection down
- `add($type, $name, $options = [])` - Add new connection
- `modify($connection, $options = [])` - Modify connection
- `clone($original, $new)` - Clone connection
- `edit($connection)` - Get edit command (interactive)
- `delete($connection)` - Delete connection
- `monitor()` - Get monitor command (interactive)
- `reload()` - Reload connections
- `load($filename)` - Load connection from file
- `import($type, $filename)` - Import connection
- `export($connection, $filename)` - Export connection

### Device Methods
- `getDevices()` - Get all devices as objects
- `getDeviceDetails($device = null)` - Get device details
- `connectDevice($device, $connection = null)` - Connect device
- `disconnectDevice($device)` - Disconnect device

### WiFi Methods
- `getWifiNetworks($device = null)` - Get available WiFi networks
- `connectWifi($ssid, $password = null, $device = null)` - Connect to WiFi
- `createHotspot($ssid, $password = null, $device = null)` - Create WiFi hotspot

### Utility Methods
- `getLastOutput()` - Get last command output
- `getLastReturnCode()` - Get last command return code
- `setUseSudo($useSudo)` - Set sudo usage
- `isUsingSudo()` - Get current sudo setting

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

## Security Considerations

- All user inputs are properly escaped using `escapeshellarg()`
- Commands are executed through PHP's `exec()` function
- Consider running with appropriate user permissions for network operations
- Be cautious when using sudo - ensure your application has proper access controls

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Changelog

### 1.0.0 - 2025-10-01
- Initial release
- Full nmcli command support
- Object-oriented interface
- Custom exception handling
- PSR-4 autoloading
- Comprehensive test suite