<?php

namespace Tandrezone\NmcliPhp;

/**
 * Represents a single NetworkManager device
 */
class Device
{
    private $data;
    private $nmcli;

    /**
     * Constructor
     * 
     * @param array $data Device data from nmcli
     * @param Nmcli $nmcli Reference to the main Nmcli instance
     */
    public function __construct(array $data, Nmcli $nmcli)
    {
        $this->data = $data;
        $this->nmcli = $nmcli;
    }

    /**
     * Get device name
     */
    public function getName()
    {
        return $this->data['DEVICE'] ?? '';
    }

    /**
     * Get device type
     */
    public function getType()
    {
        return $this->data['TYPE'] ?? '';
    }

    /**
     * Get device state
     */
    public function getState()
    {
        return $this->data['STATE'] ?? '';
    }

    /**
     * Get connected connection name
     */
    public function getConnection()
    {
        return $this->data['CONNECTION'] ?? '';
    }

    /**
     * Check if device is connected
     */
    public function isConnected()
    {
        $state = strtolower($this->getState());
        return in_array($state, ['connected', 'activated', 'up']);
    }

    /**
     * Check if device is available
     */
    public function isAvailable()
    {
        $state = strtolower($this->getState());
        return !in_array($state, ['unavailable', 'unmanaged']);
    }

    /**
     * Check if device is WiFi
     */
    public function isWifi()
    {
        return strtolower($this->getType()) === 'wifi';
    }

    /**
     * Check if device is Ethernet
     */
    public function isEthernet()
    {
        return strtolower($this->getType()) === 'ethernet';
    }

    /**
     * Get specific device property
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Connect this device
     * 
     * @param string|null $connection Optional connection name to use
     */
    public function connect($connection = null)
    {
        return $this->nmcli->connectDevice($this->getName(), $connection);
    }

    /**
     * Disconnect this device
     */
    public function disconnect()
    {
        return $this->nmcli->disconnectDevice($this->getName());
    }

    /**
     * Get detailed information about this device
     */
    public function getDetails()
    {
        return $this->nmcli->getDeviceDetails($this->getName());
    }

    /**
     * Get WiFi networks (only for WiFi devices)
     */
    public function getWifiNetworks()
    {
        if (!$this->isWifi()) {
            return [];
        }
        
        return $this->nmcli->getWifiNetworks($this->getName());
    }

    /**
     * Connect to WiFi network (only for WiFi devices)
     * 
     * @param string $ssid Network SSID
     * @param string|null $password Network password
     */
    public function connectWifi($ssid, $password = null)
    {
        if (!$this->isWifi()) {
            return false;
        }
        
        return $this->nmcli->connectWifi($ssid, $password, $this->getName());
    }

    /**
     * Create WiFi hotspot (only for WiFi devices)
     * 
     * @param string $ssid Hotspot SSID
     * @param string|null $password Hotspot password
     */
    public function createHotspot($ssid, $password = null)
    {
        if (!$this->isWifi()) {
            return false;
        }
        
        return $this->nmcli->createHotspot($ssid, $password, $this->getName());
    }

    /**
     * Refresh this device's data from nmcli
     */
    public function refresh()
    {
        $devices = $this->nmcli->getDevicesData();
        foreach ($devices as $deviceData) {
            if (($deviceData['DEVICE'] ?? '') === $this->getName()) {
                $this->data = $deviceData;
                break;
            }
        }
        return $this;
    }

    /**
     * Convert to string representation
     */
    public function __toString()
    {
        return sprintf(
            "Device[%s] Type: %s, State: %s, Connection: %s",
            $this->getName(),
            $this->getType(),
            $this->getState(),
            $this->getConnection() ?: 'None'
        );
    }

    /**
     * Convert to array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Check if device has a specific property
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Magic getter for device properties
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Magic isset for device properties
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
}