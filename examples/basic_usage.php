<?php

require_once __DIR__ . '/vendor/autoload.php';

use Tandrezone\NmcliPhp\Nmcli;
use Tandrezone\NmcliPhp\NmcliException;

// Create nmcli instance
$nmcli = new Nmcli(); // Uses sudo by default

try {
    echo "=== Network Connections ===\n";
    $connections = $nmcli->getConnections();
    foreach ($connections as $connection) {
        echo "Name: " . ($connection['NAME'] ?? 'N/A') . "\n";
        echo "Type: " . ($connection['TYPE'] ?? 'N/A') . "\n";
        echo "Device: " . ($connection['DEVICE'] ?? 'N/A') . "\n";
        echo "State: " . ($connection['STATE'] ?? 'N/A') . "\n";
        echo "---\n";
    }

    echo "\n=== Network Devices ===\n";
    $devices = $nmcli->getDevices();
    foreach ($devices as $device) {
        echo "Device: " . ($device['DEVICE'] ?? 'N/A') . "\n";
        echo "Type: " . ($device['TYPE'] ?? 'N/A') . "\n";
        echo "State: " . ($device['STATE'] ?? 'N/A') . "\n";
        echo "Connection: " . ($device['CONNECTION'] ?? 'N/A') . "\n";
        echo "---\n";
    }

    // Example: Show detailed connection info
    echo "\n=== Connection Details ===\n";
    $details = $nmcli->show(); // Show all connections
    print_r($details);

    // Example: Get WiFi networks
    echo "\n=== Available WiFi Networks ===\n";
    $wifiNetworks = $nmcli->getWifiNetworks();
    foreach ($wifiNetworks as $network) {
        echo "SSID: " . ($network['SSID'] ?? 'N/A') . "\n";
        echo "Signal: " . ($network['SIGNAL'] ?? 'N/A') . "\n";
        echo "Security: " . ($network['SECURITY'] ?? 'N/A') . "\n";
        echo "---\n";
    }

    // Example operations (commented out for safety):
    /*
    // Create a new connection
    $nmcli->add('wifi', 'MyConnection', [
        'ifname' => 'wlan0',
        'ssid' => 'MyWiFi',
        'password' => 'mypassword'
    ]);

    // Modify a connection
    $nmcli->modify('MyConnection', [
        'ipv4.method' => 'manual',
        'ipv4.addresses' => '192.168.1.100/24'
    ]);

    // Bring connection up/down
    $nmcli->up('MyConnection');
    $nmcli->down('MyConnection');

    // Clone a connection
    $nmcli->clone('MyConnection', 'MyConnection-Backup');

    // Delete a connection
    $nmcli->delete('MyConnection-Backup');

    // Connect to WiFi
    $nmcli->connectWifi('WiFiSSID', 'password');

    // Create hotspot
    $nmcli->createHotspot('MyHotspot', 'hotspotpassword');

    // Import/Export connections
    $nmcli->export('MyConnection', '/path/to/backup.nmconnection');
    $nmcli->import('openvpn', '/path/to/vpn.ovpn');

    // Reload connections
    $nmcli->reload();
    */

} catch (NmcliException $e) {
    echo "Nmcli Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

// Example: Using without sudo
echo "\n=== Using without sudo ===\n";
$nmcliNoSudo = new Nmcli(false);
try {
    $connections = $nmcliNoSudo->getConnections();
    echo "Found " . count($connections) . " connections without sudo\n";
} catch (NmcliException $e) {
    echo "Error (expected if user doesn't have permissions): " . $e->getMessage() . "\n";
}

// Interactive commands
echo "\n=== Interactive Commands ===\n";
echo "To edit a connection interactively, run: " . $nmcli->edit('your-connection-name') . "\n";
echo "To monitor connections, run: " . $nmcli->monitor() . "\n";
?>