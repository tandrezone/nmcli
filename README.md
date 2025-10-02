# nmcli-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tandrezone/nmcli-php.svg?style=flat-square)](https://packagist.org/packages/tandrezone/nmcli-php)
[![Total Downloads](https://img.shields.io/packagist/dt/tandrezone/nmcli-php.svg?style=flat-square)](https://packagist.org/packages/tandrezone/nmcli-php)
[![License](https://img.shields.io/packagist/l/tandrezone/nmcli-php.svg?style=flat-square)](https://packagist.org/packages/tandrezone/nmcli-php)

A comprehensive PHP wrapper for NetworkManager's `nmcli` command-line tool. This library provides an object-oriented interface to manage network connections, devices, and WiFi operations on Linux systems.

## Features

- **Object-Oriented Network Management**: Connections, devices, and WiFi networks as objects with built-in methods
- **Connection Management**: List, show, add, modify, clone, delete connections via `Connection` objects
- **Device Management**: List devices, connect/disconnect, show device details via `Device` objects  
- **WiFi Operations**: List networks, connect to WiFi, create hotspots via `WifiNetwork` objects
- **Import/Export**: Import and export connection configurations
- **Structured Data**: Returns objects with structured properties instead of raw command output
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
use Tandrezone\NmcliPhp\Connection;
use Tandrezone\NmcliPhp\NmcliException;

// Create nmcli instance (uses sudo by default)
$nmcli = new Nmcli();

// Or create without sudo
$nmcli = new Nmcli(false);

try {
    // Get all connections as Connection objects
    $connections = $nmcli->getConnections();
    foreach ($connections as $connection) {
        /** @var Connection $connection */
        echo $connection->getName() . " - " . $connection->getState() . "\n";
        echo "  Type: " . $connection->getType() . "\n";
        echo "  Device: " . $connection->getDevice() . "\n";
        echo "  Active: " . ($connection->isActive() ? "Yes" : "No") . "\n";
    }

    // Get all devices as Device objects
    $devices = $nmcli->getDevices();
    foreach ($devices as $device) {
        echo $device->DEVICE . " (" . $device->TYPE . ") - " . $device->STATE . "\n";
    }
} catch (NmcliException $e) {
    echo "Error: " . $e->getMessage();
}
}
```

### Object Classes

The library provides three main object classes for intuitive network management:

#### Connection Objects

```php
// Get connections as Connection objects
$connections = $nmcli->getConnections();

foreach ($connections as $connection) {
    echo "Connection: " . $connection->NAME . "\n";
    echo "Type: " . $connection->TYPE . "\n";
    echo "Device: " . $connection->DEVICE . "\n";
    
    // Use object methods for control
    if ($connection->STATE !== 'activated') {
        $connection->up();  // Bring connection up
    }
    
    // Modify and manage connections
    $connection->modify('ipv4.addresses', '192.168.1.100/24');
    $connection->down();   // Take down
    $connection->delete(); // Remove connection
}
```

#### Device Objects

```php
// Get devices as Device objects
$devices = $nmcli->getDevices();

foreach ($devices as $device) {
    echo "Device: " . $device->DEVICE . "\n";
    echo "Type: " . $device->TYPE . "\n";
    echo "State: " . $device->STATE . "\n";
    
    // Device-specific methods
    if ($device->isWifi() && $device->STATE === 'disconnected') {
        $device->connect('MyWiFiNetwork');
    }
    
    if ($device->isEthernet()) {
        echo "Ethernet device found\n";
    }
}

// Get WiFi networks for a WiFi device
$wifiDevice = $devices[0]; // Assuming first device is WiFi
if ($wifiDevice->isWifi()) {
    $networks = $wifiDevice->getWifiNetworks();
    echo "Found " . count($networks) . " WiFi networks\n";
}
```

#### WiFi Network Objects

```php
// Get available WiFi networks as WifiNetwork objects
$wifiNetworks = $nmcli->getWifiNetworks();

foreach ($wifiNetworks as $network) {
    echo "SSID: " . $network->SSID . "\n";
    echo "Signal: " . $network->SIGNAL . "% (" . $network->getSignalQuality() . ")\n";
    echo "Security: " . ($network->isSecured() ? "Secured" : "Open") . "\n";
    
    // Connect to networks with strong signal
    if ($network->hasStrongSignal() && $network->SSID === 'MyHomeWiFi') {
        $network->connect('mypassword123');
    }
}
```

### Connection Management

```php
// Get a specific connection by name
$connection = $nmcli->getConnection('MyWiFi');

if ($connection) {
    // Connection object methods
    echo "Connection: " . $connection->getName() . "\n";
    echo "Type: " . $connection->getType() . "\n";
    echo "State: " . $connection->getState() . "\n";
    echo "Is Active: " . ($connection->isActive() ? "Yes" : "No") . "\n";

    // Control the connection
    $connection->up();    // Bring connection up
    $connection->down();  // Bring connection down

    // Modify connection settings
    $connection->modify([
        'ipv4.method' => 'manual',
        'ipv4.addresses' => '192.168.1.100/24'
    ]);

    // Clone the connection
    $connection->clone('MyWiFi-Backup');

    // Export connection to file
    $connection->export('/path/to/backup.nmconnection');

    // Delete the connection
    $connection->delete();
}

// Traditional nmcli class methods still work
$success = $nmcli->add('wifi', 'NewWiFi', [
    'ifname' => 'wlan0',
    'ssid' => 'NetworkName',
    'password' => 'password123'
]);

// Show connection details (returns array data)
$details = $nmcli->show('MyWiFi');
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

### Connection Class

The `Connection` class represents individual network connections as objects:

#### Properties Access
- `getName()` - Get connection name
- `getUuid()` - Get connection UUID
- `getType()` - Get connection type (wifi, ethernet, etc.)
- `getDevice()` - Get associated device
- `getState()` - Get connection state
- `isActive()` - Check if connection is active
- `get($key)` - Get specific property value
- `has($key)` - Check if property exists

#### Connection Control
- `up()` - Bring connection up
- `down()` - Bring connection down
- `modify($options)` - Modify connection settings
- `delete()` - Delete connection
- `clone($newName)` - Clone connection
- `export($filename)` - Export connection to file

#### Information & Utilities
- `show()` - Get detailed connection information
- `refresh()` - Refresh connection data from nmcli
- `getEditCommand()` - Get interactive edit command
- `toArray()` - Convert to array
- `__toString()` - String representation

#### Magic Methods
- `$connection->NAME` - Access properties directly
- `isset($connection->UUID)` - Check property existence

### Device Class

The `Device` class represents network devices (WiFi adapters, Ethernet interfaces, etc.).

#### Properties Access
```php
echo $device->DEVICE;      // Device name (e.g., 'wlp2s0')
echo $device->TYPE;        // Device type (e.g., 'wifi', 'ethernet')
echo $device->STATE;       // Device state (e.g., 'connected', 'disconnected')
echo $device->CONNECTION;  // Active connection name
```

#### Device Control
- `connect($networkName, $password = null)` - Connect to network
- `disconnect()` - Disconnect device

#### Device Information
- `isWifi()` - Check if device is WiFi
- `isEthernet()` - Check if device is Ethernet
- `getWifiNetworks()` - Get available WiFi networks (WiFi devices only)

#### Magic Methods
- `$device->DEVICE` - Access properties directly
- `isset($device->STATE)` - Check property existence

### WifiNetwork Class

The `WifiNetwork` class represents discovered WiFi networks with signal analysis.

#### Properties Access
```php
echo $network->SSID;       // Network name
echo $network->SIGNAL;     // Signal strength (0-100)
echo $network->SECURITY;   // Security type (e.g., 'WPA2')
echo $network->CHAN;       // WiFi channel
echo $network->FREQ;       // Frequency
echo $network->CC;         // Country code
```

#### Network Analysis
- `getSignalQuality()` - Get signal quality description ('Excellent', 'Good', 'Fair', 'Poor')
- `isSecured()` - Check if network requires password
- `hasStrongSignal()` - Check if signal is strong (>= 70%)

#### Network Connection
- `connect($password = null)` - Connect to this WiFi network

#### Magic Methods
- `$network->SSID` - Access properties directly
- `isset($network->SIGNAL)` - Check property existence

### Nmcli Class Methods

#### Connection Methods
- `getConnections()` - Get all connections as Connection objects
- `getConnection($name)` - Get specific connection by name as Connection object
- `show($connection = null)` - Show connection details as array
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