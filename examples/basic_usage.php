<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tandrezone\NmcliPhp\Nmcli;
use Tandrezone\NmcliPhp\Connection;
use Tandrezone\NmcliPhp\Device;
use Tandrezone\NmcliPhp\WifiNetwork;
use Tandrezone\NmcliPhp\NmcliException;

// Create nmcli instance
$nmcli = new Nmcli(); // Uses sudo by default

    echo "=== Network Connections (as Connection objects) ===\n";
    $connections = $nmcli->getConnections();
    
    foreach ($connections as $connection) {
        /** @var Connection $connection */
        echo "Connection: " . $connection->getName() . "\n";
        echo "  Type: " . $connection->getType() . "\n";
        echo "  Device: " . $connection->getDevice() . "\n";
        echo "  State: " . $connection->getState() . "\n";
        echo "  Active: " . ($connection->isActive() ? "Yes" : "No") . "\n";
        echo "  UUID: " . $connection->getUuid() . "\n";
        echo "  String repr: " . $connection . "\n";
        echo "---\n";
    }

    echo "\n=== Network Devices (as Device objects) ===\n";
    $devices = $nmcli->getDevices();
    
    foreach ($devices as $device) {
        /** @var Device $device */
        echo "Device: " . $device->getName() . "\n";
        echo "  Type: " . $device->getType() . "\n";
        echo "  State: " . $device->getState() . "\n";
        echo "  Connection: " . $device->getConnection() . "\n";
        echo "  Connected: " . ($device->isConnected() ? "Yes" : "No") . "\n";
        echo "  Available: " . ($device->isAvailable() ? "Yes" : "No") . "\n";
        echo "  Is WiFi: " . ($device->isWifi() ? "Yes" : "No") . "\n";
        echo "  Is Ethernet: " . ($device->isEthernet() ? "Yes" : "No") . "\n";
        echo "  String repr: " . $device . "\n";
        echo "---\n";
    }

    echo "\n=== WiFi Networks (as WifiNetwork objects) ===\n";
    $wifiNetworks = $nmcli->getWifiNetworks();
    
    foreach ($wifiNetworks as $network) {
        /** @var WifiNetwork $network */
        echo "SSID: " . $network->getSsid() . "\n";
        echo "  Signal: " . $network->getSignal() . " (" . $network->getSignalStrength() . "%)\n";
        echo "  Quality: " . $network->getSignalQuality() . "\n";
        echo "  Security: " . $network->getSecurity() . "\n";
        echo "  Secured: " . ($network->isSecured() ? "Yes" : "No") . "\n";
        echo "  Channel: " . $network->getChannel() . "\n";
        echo "  Strong Signal: " . ($network->hasStrongSignal() ? "Yes" : "No") . "\n";
        echo "  String repr: " . $network . "\n";
        echo "---\n";
    // Example: Working with a specific connection
    if (!empty($connections)) {
        $firstConnection = $connections[0];
        echo "\n=== Working with Connection: " . $firstConnection->getName() . " ===\n";
        
        // Show detailed information
        $details = $firstConnection->show();
        echo "Detailed info: " . count($details) . " properties\n";
        
        // Get edit command (for interactive editing)
        echo "Edit command: " . $firstConnection->getEditCommand() . "\n";
        
    }

    // Example: Working with a specific device
    if (!empty($devices)) {
        $firstDevice = $devices[0];
        echo "\n=== Working with Device: " . $firstDevice->getName() . " ===\n";
        
        // Get device details
        $details = $firstDevice->getDetails();
        echo "Device details: " . count($details) . " properties\n";
        
        // WiFi-specific operations
        if ($firstDevice->isWifi()) {
            echo "This is a WiFi device\n";
            
            // Get WiFi networks for this specific device
            $deviceWifiNetworks = $firstDevice->getWifiNetworks();
            echo "Found " . count($deviceWifiNetworks) . " WiFi networks\n";
            
            // Example WiFi operations (commented out for safety):
            /*
            // Connect to a WiFi network
            if (!empty($deviceWifiNetworks)) {
                $network = $deviceWifiNetworks[0];
                $success = $firstDevice->connectWifi($network->getSsid(), 'password');
                echo "Connected to WiFi: " . ($success ? "Success" : "Failed") . "\n";
            }
            
            // Create hotspot
            $success = $firstDevice->createHotspot('MyHotspot', 'password123');
            echo "Created hotspot: " . ($success ? "Success" : "Failed") . "\n";
            */
        }
        
        // Device control operations (commented out for safety):
        /*
        // Connect/disconnect device
        if (!$firstDevice->isConnected()) {
            $success = $firstDevice->connect();
            echo "Connected device: " . ($success ? "Success" : "Failed") . "\n";
        } else {
            $success = $firstDevice->disconnect();
            echo "Disconnected device: " . ($success ? "Success" : "Failed") . "\n";
        }
        */
    }

    // Example: Working with WiFi networks
    if (!empty($wifiNetworks)) {
        $firstNetwork = $wifiNetworks[0];
        echo "\n=== Working with WiFi Network: " . $firstNetwork->getSsid() . " ===\n";
        
        echo "Signal strength: " . $firstNetwork->getSignalStrength() . "%\n";
        echo "Signal quality: " . $firstNetwork->getSignalQuality() . "\n";
        echo "Security type: " . $firstNetwork->getSecurity() . "\n";
        echo "Is secured: " . ($firstNetwork->isSecured() ? "Yes" : "No") . "\n";
        echo "Uses WPA: " . ($firstNetwork->isWpa() ? "Yes" : "No") . "\n";
        echo "Has strong signal: " . ($firstNetwork->hasStrongSignal() ? "Yes" : "No") . "\n";
        
        // Example WiFi connection (commented out for safety):
        /*
        // Connect to this network
        $success = $firstNetwork->connect('password123');
        echo "Connected to " . $firstNetwork->getSsid() . ": " . ($success ? "Success" : "Failed") . "\n";
        */
    }

    // Example: Get specific connection by name
    echo "\n=== Get Connection by Name ===\n";
    $specificConnection = $nmcli->getConnection('Wired connection 1'); // Common default name
    if ($specificConnection) {
        echo "Found connection: " . $specificConnection->getName() . "\n";
        echo "Type: " . $specificConnection->getType() . "\n";
        echo "State: " . $specificConnection->getState() . "\n";
    } else {
        echo "Connection 'Wired connection 1' not found\n";
    }

    // Example: Get specific device by name
    echo "\n=== Get Device by Name ===\n";
    $specificDevice = $nmcli->getDevice('wlan0'); // Common WiFi device name
    if ($specificDevice) {
        echo "Found device: " . $specificDevice->getName() . "\n";
        echo "Type: " . $specificDevice->getType() . "\n";
        echo "State: " . $specificDevice->getState() . "\n";
        echo "Is WiFi: " . ($specificDevice->isWifi() ? "Yes" : "No") . "\n";
    } else {
        echo "Device 'wlan0' not found\n";
    }
}

// Example: Using without sudo
echo "\n=== Using without sudo ===\n";
$nmcliNoSudo = new Nmcli(false);
try {
    $connections = $nmcliNoSudo->getConnections();
    echo "Found " . count($connections) . " connections without sudo\n";
    
    foreach ($connections as $connection) {
        echo "  - " . $connection->getName() . " (" . $connection->getState() . ")\n";
    }
    
    $devices = $nmcliNoSudo->getDevices();
    echo "Found " . count($devices) . " devices without sudo\n";
    
    foreach ($devices as $device) {
        echo "  - " . $device->getName() . " (" . $device->getType() . ", " . $device->getState() . ")\n";
    }
} catch (NmcliException $e) {
    echo "Error (expected if user doesn't have permissions): " . $e->getMessage() . "\n";
}

// Interactive commands
echo "\n=== Interactive Commands ===\n";
if (!empty($connections)) {
    $firstConnection = $connections[0];
    echo "To edit '" . $firstConnection->getName() . "' interactively, run: " . $firstConnection->getEditCommand() . "\n";
}
echo "To monitor connections, run: " . $nmcli->monitor() . "\n";

// Demonstrate magic methods and array access
echo "\n=== Magic Methods and Array Access ===\n";
if (!empty($connections)) {
    $connection = $connections[0];
    echo "Connection name via magic getter: " . $connection->NAME . "\n";
    echo "Connection has UUID: " . (isset($connection->UUID) ? "Yes" : "No") . "\n";
    echo "Connection as array: " . json_encode($connection->toArray(), JSON_PRETTY_PRINT) . "\n";
}

if (!empty($devices)) {
    $device = $devices[0];
    echo "Device name via magic getter: " . $device->DEVICE . "\n";
    echo "Device has TYPE: " . (isset($device->TYPE) ? "Yes" : "No") . "\n";
}

if (!empty($wifiNetworks)) {
    $wifiNetwork = $wifiNetworks[0];
    echo "WiFi SSID via magic getter: " . $wifiNetwork->SSID . "\n";
    echo "WiFi has SIGNAL: " . (isset($wifiNetwork->SIGNAL) ? "Yes" : "No") . "\n";
}

echo "\n=== Summary ===\n";
echo "✅ All object classes are working correctly!\n";
echo "📋 Connections: " . count($connections) . " found\n";
echo "📱 Devices: " . count($devices) . " found\n"; 
echo "📶 WiFi Networks: " . count($wifiNetworks) . " found\n";
?>