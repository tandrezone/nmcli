<?php
/**
 * Object Usage Example
 * 
 * This example demonstrates how to use all three object classes:
 * - Connection objects for managing network connections
 * - Device objects for device control and information
 * - WifiNetwork objects for WiFi network discovery and connection
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Tandrezone\NmcliPhp\Nmcli;
use Tandrezone\NmcliPhp\NmcliException;

try {
    // Create nmcli instance
    $nmcli = new Nmcli();
    
    echo "=== Network Manager Object Usage Demo ===\n\n";
    
    // === CONNECTION OBJECTS ===
    echo "🔗 CONNECTION OBJECTS:\n";
    echo str_repeat("-", 50) . "\n";
    
    $connections = $nmcli->getConnections();
    echo "Found " . count($connections) . " connections:\n\n";
    
    foreach ($connections as $connection) {
        echo "• Connection: {$connection->NAME}\n";
        echo "  Type: {$connection->TYPE}\n";
        echo "  Device: {$connection->DEVICE}\n";
        echo "  State: {$connection->STATE}\n";
        echo "  Active: " . ($connection->isActive() ? "Yes" : "No") . "\n";
        
        // Example of connection control (commented to avoid actual changes)
        /*
        if (!$connection->isActive()) {
            echo "  → Bringing connection up...\n";
            $connection->up();
        }
        */
        
        echo "\n";
    }
    
    // === DEVICE OBJECTS ===
    echo "📱 DEVICE OBJECTS:\n";
    echo str_repeat("-", 50) . "\n";
    
    $devices = $nmcli->getDevices();
    echo "Found " . count($devices) . " devices:\n\n";
    
    foreach ($devices as $device) {
        echo "• Device: {$device->DEVICE}\n";
        echo "  Type: {$device->TYPE}\n";
        echo "  State: {$device->STATE}\n";
        echo "  Connection: {$device->CONNECTION}\n";
        
        // Device type checking
        if ($device->isWifi()) {
            echo "  📶 WiFi Device Features:\n";
            echo "    - Can scan for networks\n";
            echo "    - Supports wireless connections\n";
            
            // Get WiFi networks for this device (if connected)
            try {
                $wifiNetworks = $device->getWifiNetworks();
                echo "    - Found " . count($wifiNetworks) . " WiFi networks nearby\n";
            } catch (Exception $e) {
                echo "    - WiFi scan not available (device may be off)\n";
            }
        } elseif ($device->isEthernet()) {
            echo "  🔌 Ethernet Device Features:\n";
            echo "    - Wired connection support\n";
            echo "    - Typically more stable than WiFi\n";
        }
        
        echo "\n";
    }
    
    // === WIFI NETWORK OBJECTS ===
    echo "📶 WIFI NETWORK OBJECTS:\n";
    echo str_repeat("-", 50) . "\n";
    
    try {
        $wifiNetworks = $nmcli->getWifiNetworks();
        echo "Found " . count($wifiNetworks) . " WiFi networks:\n\n";
        
        foreach ($wifiNetworks as $network) {
            echo "• SSID: {$network->SSID}\n";
            echo "  Signal: {$network->SIGNAL}% ({$network->getSignalQuality()})\n";
            echo "  Security: " . ($network->isSecured() ? "🔒 Secured ({$network->SECURITY})" : "🔓 Open") . "\n";
            echo "  Channel: {$network->CHAN}\n";
            echo "  Frequency: {$network->FREQ} MHz\n";
            
            // Signal analysis
            if ($network->hasStrongSignal()) {
                echo "  💪 Strong signal - Good for connection\n";
            } else {
                echo "  📶 Weak signal - May have connectivity issues\n";
            }
            
            // Example connection (commented to avoid actual changes)
            /*
            if ($network->SSID === 'MyHomeNetwork' && $network->hasStrongSignal()) {
                echo "  → Connecting to home network...\n";
                $network->connect('mypassword123');
            }
            */
            
            echo "\n";
        }
    } catch (NmcliException $e) {
        echo "WiFi scanning not available: " . $e->getMessage() . "\n\n";
    }
    
    // === PRACTICAL EXAMPLES ===
    echo "💡 PRACTICAL EXAMPLES:\n";
    echo str_repeat("-", 50) . "\n";
    
    echo "1. Find strongest WiFi signal:\n";
    try {
        $wifiNetworks = $nmcli->getWifiNetworks();
        $strongest = null;
        $maxSignal = 0;
        
        foreach ($wifiNetworks as $network) {
            if ($network->SIGNAL > $maxSignal) {
                $maxSignal = $network->SIGNAL;
                $strongest = $network;
            }
        }
        
        if ($strongest) {
            echo "   Strongest signal: {$strongest->SSID} ({$strongest->SIGNAL}%)\n";
        }
    } catch (Exception $e) {
        echo "   WiFi scanning not available\n";
    }
    
    echo "\n2. Find WiFi devices:\n";
    $wifiDevices = array_filter($devices, fn($device) => $device->isWifi());
    echo "   Found " . count($wifiDevices) . " WiFi device(s)\n";
    
    echo "\n3. Find active connections:\n";
    $activeConnections = array_filter($connections, fn($conn) => $conn->isActive());
    echo "   Found " . count($activeConnections) . " active connection(s)\n";
    
    foreach ($activeConnections as $conn) {
        echo "   - {$conn->NAME} on {$conn->DEVICE}\n";
    }
    
    echo "\n4. Security analysis:\n";
    try {
        $wifiNetworks = $nmcli->getWifiNetworks();
        $openNetworks = array_filter($wifiNetworks, fn($net) => !$net->isSecured());
        $securedNetworks = array_filter($wifiNetworks, fn($net) => $net->isSecured());
        
        echo "   Open networks: " . count($openNetworks) . "\n";
        echo "   Secured networks: " . count($securedNetworks) . "\n";
        
        if (count($openNetworks) > 0) {
            echo "   ⚠️  Warning: Open networks detected (no password required)\n";
        }
    } catch (Exception $e) {
        echo "   WiFi analysis not available\n";
    }
    
    echo "\n=== Demo Complete ===\n";
    echo "All object classes are working correctly! 🎉\n";
    
} catch (NmcliException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure nmcli is installed and accessible.\n";
} catch (Exception $e) {
    echo "❌ Unexpected error: " . $e->getMessage() . "\n";
}