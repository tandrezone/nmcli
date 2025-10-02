<?php

namespace Tandrezone\NmcliPhp;

/**
 * Represents a WiFi network discovered by NetworkManager
 */
class WifiNetwork
{
    private $data;
    private $nmcli;
    private $device;

    /**
     * Constructor
     * 
     * @param array $data WiFi network data from nmcli
     * @param Nmcli $nmcli Reference to the main Nmcli instance
     * @param string|null $device Device name that discovered this network
     */
    public function __construct(array $data, Nmcli $nmcli, $device = null)
    {
        $this->data = $data;
        $this->nmcli = $nmcli;
        $this->device = $device;
    }

    /**
     * Get network SSID
     */
    public function getSsid()
    {
        return $this->data['SSID'] ?? '';
    }

    /**
     * Get network BSSID (MAC address)
     */
    public function getBssid()
    {
        return $this->data['BSSID'] ?? '';
    }

    /**
     * Get signal strength
     */
    public function getSignal()
    {
        return $this->data['SIGNAL'] ?? '';
    }

    /**
     * Get signal strength as integer (0-100)
     */
    public function getSignalStrength()
    {
        $signal = $this->getSignal();
        if (is_numeric($signal)) {
            return (int) $signal;
        }
        
        // Try to extract numeric value if it contains '%' or other characters
        if (preg_match('/(\d+)/', $signal, $matches)) {
            return (int) $matches[1];
        }
        
        return 0;
    }

    /**
     * Get security type
     */
    public function getSecurity()
    {
        return $this->data['SECURITY'] ?? '';
    }

    /**
     * Get network mode (Infra, Ad-Hoc, etc.)
     */
    public function getMode()
    {
        return $this->data['MODE'] ?? '';
    }

    /**
     * Get channel
     */
    public function getChannel()
    {
        return $this->data['CHAN'] ?? '';
    }

    /**
     * Get frequency
     */
    public function getFrequency()
    {
        return $this->data['FREQ'] ?? '';
    }

    /**
     * Get data rate
     */
    public function getRate()
    {
        return $this->data['RATE'] ?? '';
    }

    /**
     * Check if network is secured
     */
    public function isSecured()
    {
        $security = strtolower($this->getSecurity());
        return !empty($security) && $security !== '--' && $security !== 'none';
    }

    /**
     * Check if network is open (no security)
     */
    public function isOpen()
    {
        return !$this->isSecured();
    }

    /**
     * Check if network uses WPA/WPA2
     */
    public function isWpa()
    {
        $security = strtolower($this->getSecurity());
        return strpos($security, 'wpa') !== false;
    }

    /**
     * Check if network uses WEP
     */
    public function isWep()
    {
        $security = strtolower($this->getSecurity());
        return strpos($security, 'wep') !== false;
    }

    /**
     * Check if signal is strong (>= 70%)
     */
    public function hasStrongSignal()
    {
        return $this->getSignalStrength() >= 70;
    }

    /**
     * Check if signal is good (>= 50%)
     */
    public function hasGoodSignal()
    {
        return $this->getSignalStrength() >= 50;
    }

    /**
     * Check if signal is weak (< 30%)
     */
    public function hasWeakSignal()
    {
        return $this->getSignalStrength() < 30;
    }

    /**
     * Get signal quality description
     */
    public function getSignalQuality()
    {
        $strength = $this->getSignalStrength();
        
        if ($strength >= 80) return 'Excellent';
        if ($strength >= 70) return 'Very Good';
        if ($strength >= 60) return 'Good';
        if ($strength >= 50) return 'Fair';
        if ($strength >= 30) return 'Weak';
        return 'Very Weak';
    }

    /**
     * Get specific network property
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Connect to this WiFi network
     * 
     * @param string|null $password Network password (if required)
     * @param string|null $device Specific device to use (if not set during construction)
     */
    public function connect($password = null, $device = null)
    {
        $deviceToUse = $device ?: $this->device;
        return $this->nmcli->connectWifi($this->getSsid(), $password, $deviceToUse);
    }

    /**
     * Check if this network is currently connected
     */
    public function isConnected()
    {
        // Get device connections to check if this SSID is currently connected
        $devices = $this->nmcli->getDevices();
        foreach ($devices as $device) {
            if ($device instanceof Device && $device->isWifi() && $device->isConnected()) {
                // Try to match by checking if connection name contains SSID
                $connectionName = $device->getConnection();
                if (strpos($connectionName, $this->getSsid()) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Convert to string representation
     */
    public function __toString()
    {
        return sprintf(
            "WiFi[%s] Signal: %s%%, Security: %s, Quality: %s",
            $this->getSsid(),
            $this->getSignalStrength(),
            $this->getSecurity() ?: 'Open',
            $this->getSignalQuality()
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
     * Check if network has a specific property
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Magic getter for network properties
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Magic isset for network properties
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Compare networks by signal strength (for sorting)
     */
    public function compareBySignal(WifiNetwork $other)
    {
        return $other->getSignalStrength() - $this->getSignalStrength();
    }

    /**
     * Compare networks by SSID (for sorting)
     */
    public function compareBySsid(WifiNetwork $other)
    {
        return strcmp($this->getSsid(), $other->getSsid());
    }
}