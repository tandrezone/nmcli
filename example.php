<?php
require_once 'nmcli.php';

// Create nmcli instance
$nmcli = new Nmcli();

try {
    echo "=== Network Connections ===\n";
    $connections = $nmcli->getConnections();
    foreach ($connections as $connection) {
        echo "Name: " . $connection['NAME'] . "\n";
        echo "Type: " . $connection['TYPE'] . "\n";
        echo "Device: " . $connection['DEVICE'] . "\n";
        echo "State: " . $connection['STATE'] . "\n";
        echo "---\n";
    }

    echo "\n=== Network Devices ===\n";
    $devices = $nmcli->getDevices();
    foreach ($devices as $device) {
        echo "Device: " . $device['DEVICE'] . "\n";
        echo "Type: " . $device['TYPE'] . "\n";
        echo "State: " . $device['STATE'] . "\n";
        echo "Connection: " . $device['CONNECTION'] . "\n";
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
        echo "SSID: " . $network['SSID'] . "\n";
        echo "Signal: " . $network['SIGNAL'] . "\n";
        echo "Security: " . $network['SECURITY'] . "\n";
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

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>