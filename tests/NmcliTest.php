<?php

namespace Tandrezone\NmcliPhp\Tests;

use PHPUnit\Framework\TestCase;
use Tandrezone\NmcliPhp\Nmcli;
use Tandrezone\NmcliPhp\Connection;
use Tandrezone\NmcliPhp\Device;
use Tandrezone\NmcliPhp\WifiNetwork;
use Tandrezone\NmcliPhp\NmcliException;

class NmcliTest extends TestCase
{
    private $nmcli;

    protected function setUp(): void
    {
        $this->nmcli = new Nmcli(false); // Don't use sudo for tests
    }

    public function testConstructor()
    {
        $nmcli = new Nmcli();
        $this->assertTrue($nmcli->isUsingSudo());

        $nmcli = new Nmcli(false);
        $this->assertFalse($nmcli->isUsingSudo());
    }

    public function testSetUseSudo()
    {
        $this->nmcli->setUseSudo(true);
        $this->assertTrue($this->nmcli->isUsingSudo());

        $this->nmcli->setUseSudo(false);
        $this->assertFalse($this->nmcli->isUsingSudo());
    }

    public function testGetConnections()
    {
        // This test would require nmcli to be available
        // In a real test environment, you might mock the exec function
        $connections = $this->nmcli->getConnections();
        $this->assertIsArray($connections);
        
        // If connections are returned, they should be Connection objects
        if (!empty($connections)) {
            $this->assertInstanceOf(Connection::class, $connections[0]);
        }
    }

    public function testGetDevices()
    {
        // This test would require nmcli to be available
        // In a real test environment, you might mock the exec function
        $devices = $this->nmcli->getDevices();
        $this->assertIsArray($devices);
        
        // If devices are returned, they should be Device objects
        if (!empty($devices)) {
            $this->assertInstanceOf(Device::class, $devices[0]);
        }
    }

    public function testGetWifiNetworks()
    {
        // This test would require nmcli to be available
        $wifiNetworks = $this->nmcli->getWifiNetworks();
        $this->assertIsArray($wifiNetworks);
        
        // If networks are returned, they should be WifiNetwork objects
        if (!empty($wifiNetworks)) {
            $this->assertInstanceOf(WifiNetwork::class, $wifiNetworks[0]);
        }
    }

    public function testEditReturnsCommand()
    {
        $command = $this->nmcli->edit('test-connection');
        $this->assertStringContainsString('nmcli con edit', $command);
        $this->assertStringContainsString('test-connection', $command);
    }

    public function testMonitorReturnsCommand()
    {
        $command = $this->nmcli->monitor();
        $this->assertEquals('nmcli con monitor', $command);
    }

    public function testEditWithSudo()
    {
        $this->nmcli->setUseSudo(true);
        $command = $this->nmcli->edit('test-connection');
        $this->assertStringContainsString('sudo nmcli con edit', $command);
    }

    public function testMonitorWithSudo()
    {
        $this->nmcli->setUseSudo(true);
        $command = $this->nmcli->monitor();
        $this->assertEquals('sudo nmcli con monitor', $command);
    }

    public function testConnectionClass()
    {
        // Test Connection class with mock data
        $mockData = [
            'NAME' => 'Test Connection',
            'UUID' => '12345678-1234-1234-1234-123456789abc',
            'TYPE' => 'wifi',
            'DEVICE' => 'wlan0',
            'STATE' => 'activated'
        ];

        $connection = new Connection($mockData, $this->nmcli);

        $this->assertEquals('Test Connection', $connection->getName());
        $this->assertEquals('12345678-1234-1234-1234-123456789abc', $connection->getUuid());
        $this->assertEquals('wifi', $connection->getType());
        $this->assertEquals('wlan0', $connection->getDevice());
        $this->assertEquals('activated', $connection->getState());
        $this->assertTrue($connection->isActive());
        $this->assertEquals('Test Connection', $connection->get('NAME'));
        $this->assertTrue($connection->has('NAME'));
        $this->assertFalse($connection->has('NONEXISTENT'));
    }

    public function testDeviceClass()
    {
        // Test Device class with mock data
        $mockData = [
            'DEVICE' => 'wlan0',
            'TYPE' => 'wifi',
            'STATE' => 'connected',
            'CONNECTION' => 'MyWiFi'
        ];

        $device = new Device($mockData, $this->nmcli);

        $this->assertEquals('wlan0', $device->getName());
        $this->assertEquals('wifi', $device->getType());
        $this->assertEquals('connected', $device->getState());
        $this->assertEquals('MyWiFi', $device->getConnection());
        $this->assertTrue($device->isConnected());
        $this->assertTrue($device->isAvailable());
        $this->assertTrue($device->isWifi());
        $this->assertFalse($device->isEthernet());
        $this->assertEquals('wlan0', $device->get('DEVICE'));
        $this->assertTrue($device->has('DEVICE'));
        $this->assertFalse($device->has('NONEXISTENT'));
    }

    public function testWifiNetworkClass()
    {
        // Test WifiNetwork class with mock data
        $mockData = [
            'SSID' => 'TestNetwork',
            'BSSID' => '00:11:22:33:44:55',
            'SIGNAL' => '85',
            'SECURITY' => 'WPA2',
            'MODE' => 'Infra',
            'CHAN' => '6',
            'FREQ' => '2437 MHz',
            'RATE' => '54 Mbit/s'
        ];

        $wifiNetwork = new WifiNetwork($mockData, $this->nmcli, 'wlan0');

        $this->assertEquals('TestNetwork', $wifiNetwork->getSsid());
        $this->assertEquals('00:11:22:33:44:55', $wifiNetwork->getBssid());
        $this->assertEquals('85', $wifiNetwork->getSignal());
        $this->assertEquals(85, $wifiNetwork->getSignalStrength());
        $this->assertEquals('WPA2', $wifiNetwork->getSecurity());
        $this->assertEquals('Infra', $wifiNetwork->getMode());
        $this->assertEquals('6', $wifiNetwork->getChannel());
        $this->assertTrue($wifiNetwork->isSecured());
        $this->assertFalse($wifiNetwork->isOpen());
        $this->assertTrue($wifiNetwork->isWpa());
        $this->assertFalse($wifiNetwork->isWep());
        $this->assertTrue($wifiNetwork->hasStrongSignal());
        $this->assertTrue($wifiNetwork->hasGoodSignal());
        $this->assertFalse($wifiNetwork->hasWeakSignal());
        $this->assertEquals('Excellent', $wifiNetwork->getSignalQuality());
    }

    public function testConnectionMagicMethods()
    {
        $mockData = [
            'NAME' => 'Test Connection',
            'TYPE' => 'wifi'
        ];

        $connection = new Connection($mockData, $this->nmcli);

        // Test magic getter
        $this->assertEquals('Test Connection', $connection->NAME);
        $this->assertEquals('wifi', $connection->TYPE);
        $this->assertNull($connection->NONEXISTENT);

        // Test magic isset
        $this->assertTrue(isset($connection->NAME));
        $this->assertFalse(isset($connection->NONEXISTENT));
    }

    public function testDeviceMagicMethods()
    {
        $mockData = [
            'DEVICE' => 'wlan0',
            'TYPE' => 'wifi'
        ];

        $device = new Device($mockData, $this->nmcli);

        // Test magic getter
        $this->assertEquals('wlan0', $device->DEVICE);
        $this->assertEquals('wifi', $device->TYPE);
        $this->assertNull($device->NONEXISTENT);

        // Test magic isset
        $this->assertTrue(isset($device->DEVICE));
        $this->assertFalse(isset($device->NONEXISTENT));
    }

    public function testWifiNetworkMagicMethods()
    {
        $mockData = [
            'SSID' => 'TestNetwork',
            'SIGNAL' => '75'
        ];

        $wifiNetwork = new WifiNetwork($mockData, $this->nmcli);

        // Test magic getter
        $this->assertEquals('TestNetwork', $wifiNetwork->SSID);
        $this->assertEquals('75', $wifiNetwork->SIGNAL);
        $this->assertNull($wifiNetwork->NONEXISTENT);

        // Test magic isset
        $this->assertTrue(isset($wifiNetwork->SSID));
        $this->assertFalse(isset($wifiNetwork->NONEXISTENT));
    }

    public function testConnectionToString()
    {
        $mockData = [
            'NAME' => 'Test Connection',
            'TYPE' => 'wifi',
            'DEVICE' => 'wlan0',
            'STATE' => 'activated'
        ];

        $connection = new Connection($mockData, $this->nmcli);
        $stringRepr = (string) $connection;

        $this->assertStringContainsString('Test Connection', $stringRepr);
        $this->assertStringContainsString('wifi', $stringRepr);
        $this->assertStringContainsString('wlan0', $stringRepr);
        $this->assertStringContainsString('activated', $stringRepr);
    }

    public function testDeviceToString()
    {
        $mockData = [
            'DEVICE' => 'wlan0',
            'TYPE' => 'wifi',
            'STATE' => 'connected',
            'CONNECTION' => 'MyWiFi'
        ];

        $device = new Device($mockData, $this->nmcli);
        $stringRepr = (string) $device;

        $this->assertStringContainsString('wlan0', $stringRepr);
        $this->assertStringContainsString('wifi', $stringRepr);
        $this->assertStringContainsString('connected', $stringRepr);
        $this->assertStringContainsString('MyWiFi', $stringRepr);
    }

    public function testWifiNetworkToString()
    {
        $mockData = [
            'SSID' => 'TestNetwork',
            'SIGNAL' => '75',
            'SECURITY' => 'WPA2'
        ];

        $wifiNetwork = new WifiNetwork($mockData, $this->nmcli);
        $stringRepr = (string) $wifiNetwork;

        $this->assertStringContainsString('TestNetwork', $stringRepr);
        $this->assertStringContainsString('75', $stringRepr);
        $this->assertStringContainsString('WPA2', $stringRepr);
    }

    public function testConnectionToArray()
    {
        $mockData = [
            'NAME' => 'Test Connection',
            'TYPE' => 'wifi'
        ];

        $connection = new Connection($mockData, $this->nmcli);
        $this->assertEquals($mockData, $connection->toArray());
    }

    public function testDeviceToArray()
    {
        $mockData = [
            'DEVICE' => 'wlan0',
            'TYPE' => 'wifi'
        ];

        $device = new Device($mockData, $this->nmcli);
        $this->assertEquals($mockData, $device->toArray());
    }

    public function testWifiNetworkToArray()
    {
        $mockData = [
            'SSID' => 'TestNetwork',
            'SIGNAL' => '75'
        ];

        $wifiNetwork = new WifiNetwork($mockData, $this->nmcli);
        $this->assertEquals($mockData, $wifiNetwork->toArray());
    }

    public function testConnectionIsActive()
    {
        $activeStates = ['activated', 'connected', 'active'];
        $inactiveStates = ['disconnected', 'inactive', 'deactivated'];

        foreach ($activeStates as $state) {
            $connection = new Connection(['STATE' => $state], $this->nmcli);
            $this->assertTrue($connection->isActive(), "State '$state' should be active");
        }

        foreach ($inactiveStates as $state) {
            $connection = new Connection(['STATE' => $state], $this->nmcli);
            $this->assertFalse($connection->isActive(), "State '$state' should be inactive");
        }
    }

    public function testDeviceIsConnected()
    {
        $connectedStates = ['connected', 'activated', 'up'];
        $disconnectedStates = ['disconnected', 'unavailable', 'down'];

        foreach ($connectedStates as $state) {
            $device = new Device(['STATE' => $state], $this->nmcli);
            $this->assertTrue($device->isConnected(), "State '$state' should be connected");
        }

        foreach ($disconnectedStates as $state) {
            $device = new Device(['STATE' => $state], $this->nmcli);
            $this->assertFalse($device->isConnected(), "State '$state' should be disconnected");
        }
    }

    public function testWifiNetworkSignalQuality()
    {
        $signalTests = [
            ['signal' => '90', 'quality' => 'Excellent'],
            ['signal' => '75', 'quality' => 'Very Good'],
            ['signal' => '65', 'quality' => 'Good'],
            ['signal' => '55', 'quality' => 'Fair'],
            ['signal' => '35', 'quality' => 'Weak'],
            ['signal' => '15', 'quality' => 'Very Weak']
        ];

        foreach ($signalTests as $test) {
            $wifiNetwork = new WifiNetwork(['SIGNAL' => $test['signal']], $this->nmcli);
            $this->assertEquals($test['quality'], $wifiNetwork->getSignalQuality());
        }
    }

    public function testConnectionGetEditCommand()
    {
        $connection = new Connection(['NAME' => 'Test Connection'], $this->nmcli);
        $command = $connection->getEditCommand();
        $this->assertStringContainsString('nmcli con edit', $command);
        $this->assertStringContainsString('Test Connection', $command);
    }
}