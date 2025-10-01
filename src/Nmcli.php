<?php

namespace Tandrezone\NmcliPhp;

/**
 * NetworkManager CLI (nmcli) PHP Wrapper
 * Provides object-oriented interface to nmcli commands
 */
class Nmcli
{
    private $lastOutput;
    private $lastReturnCode;
    private $useSudo;

    /**
     * Constructor
     * 
     * @param bool $useSudo Whether to use sudo for nmcli commands
     */
    public function __construct($useSudo = true)
    {
        $this->useSudo = $useSudo;
    }

    /**
     * Execute nmcli command and return parsed output
     */
    private function executeCommand($command)
    {
        $prefix = $this->useSudo ? "sudo " : "";
        $fullCommand = $prefix . "nmcli " . $command;
        exec($fullCommand, $output, $returnCode);
        
        $this->lastOutput = $output;
        $this->lastReturnCode = $returnCode;
        
        if ($returnCode !== 0) {
            throw new NmcliException("nmcli command failed: " . implode("\n", $output));
        }
        
        return $output;
    }

    /**
     * Parse nmcli multiline output into associative array
     */
    private function parseOutput($output)
    {
        $result = [];
        $i = 0;
        
        foreach ($output as $line) {
            if (empty(trim($line))) {
                continue;
            }
            
            $parts = explode(":", $line, 2);
            if (count($parts) !== 2) {
                continue;
            }
            
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // If this key already exists in current item, start a new item
            if (isset($result[$i][$key])) {
                $i++;
            }
            
            $result[$i][$key] = $value;
        }
        
        return $result;
    }

    /**
     * Get all connections as objects
     */
    public function getConnections()
    {
        try {
            $output = $this->executeCommand("--mode multiline con show");
            return $this->parseOutput($output);
        } catch (NmcliException $e) {
            return [];
        }
    }

    /**
     * Get all devices as objects
     */
    public function getDevices()
    {
        try {
            $output = $this->executeCommand("--mode multiline dev status");
            return $this->parseOutput($output);
        } catch (NmcliException $e) {
            return [];
        }
    }

    /**
     * Show connection details
     */
    public function show($connection = null)
    {
        $command = "--mode multiline con show";
        if ($connection) {
            $command .= " " . escapeshellarg($connection);
        }
        
        try {
            $output = $this->executeCommand($command);
            return $this->parseOutput($output);
        } catch (NmcliException $e) {
            return [];
        }
    }

    /**
     * Bring connection up
     */
    public function up($connection)
    {
        try {
            $this->executeCommand("con up " . escapeshellarg($connection));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Bring connection down
     */
    public function down($connection)
    {
        try {
            $this->executeCommand("con down " . escapeshellarg($connection));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Add new connection
     */
    public function add($type, $conName, $options = [])
    {
        $command = "con add type " . escapeshellarg($type) . " con-name " . escapeshellarg($conName);
        
        foreach ($options as $key => $value) {
            $command .= " " . escapeshellarg($key) . " " . escapeshellarg($value);
        }
        
        try {
            $this->executeCommand($command);
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Modify existing connection
     */
    public function modify($connection, $options = [])
    {
        $command = "con modify " . escapeshellarg($connection);
        
        foreach ($options as $key => $value) {
            $command .= " " . escapeshellarg($key) . " " . escapeshellarg($value);
        }
        
        try {
            $this->executeCommand($command);
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Clone existing connection
     */
    public function clone($originalConnection, $newConnection)
    {
        try {
            $this->executeCommand("con clone " . escapeshellarg($originalConnection) . " " . escapeshellarg($newConnection));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Edit connection interactively (returns command to run manually)
     */
    public function edit($connection)
    {
        // Interactive editing can't be done through exec, return command
        $prefix = $this->useSudo ? "sudo " : "";
        return $prefix . "nmcli con edit " . escapeshellarg($connection);
    }

    /**
     * Delete connection
     */
    public function delete($connection)
    {
        try {
            $this->executeCommand("con delete " . escapeshellarg($connection));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Monitor connections (returns command to run manually as it's interactive)
     */
    public function monitor()
    {
        $prefix = $this->useSudo ? "sudo " : "";
        return $prefix . "nmcli con monitor";
    }

    /**
     * Reload connections
     */
    public function reload()
    {
        try {
            $this->executeCommand("con reload");
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Load connection from file
     */
    public function load($filename)
    {
        try {
            $this->executeCommand("con load " . escapeshellarg($filename));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Import VPN or other connection
     */
    public function import($type, $filename)
    {
        try {
            $this->executeCommand("con import type " . escapeshellarg($type) . " file " . escapeshellarg($filename));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Export connection
     */
    public function export($connection, $filename)
    {
        try {
            $this->executeCommand("con export " . escapeshellarg($connection) . " " . escapeshellarg($filename));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Get device details
     */
    public function getDeviceDetails($device = null)
    {
        $command = "--mode multiline dev show";
        if ($device) {
            $command .= " " . escapeshellarg($device);
        }
        
        try {
            $output = $this->executeCommand($command);
            return $this->parseOutput($output);
        } catch (NmcliException $e) {
            return [];
        }
    }

    /**
     * Connect device
     */
    public function connectDevice($device, $connection = null)
    {
        $command = "dev connect " . escapeshellarg($device);
        if ($connection) {
            $command .= " " . escapeshellarg($connection);
        }
        
        try {
            $this->executeCommand($command);
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Disconnect device
     */
    public function disconnectDevice($device)
    {
        try {
            $this->executeCommand("dev disconnect " . escapeshellarg($device));
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Get last command output
     */
    public function getLastOutput()
    {
        return $this->lastOutput;
    }

    /**
     * Get last command return code
     */
    public function getLastReturnCode()
    {
        return $this->lastReturnCode;
    }

    /**
     * Get WiFi networks
     */
    public function getWifiNetworks($device = null)
    {
        $command = "--mode multiline dev wifi list";
        if ($device) {
            $command .= " ifname " . escapeshellarg($device);
        }
        
        try {
            $output = $this->executeCommand($command);
            return $this->parseOutput($output);
        } catch (NmcliException $e) {
            return [];
        }
    }

    /**
     * Connect to WiFi network
     */
    public function connectWifi($ssid, $password = null, $device = null)
    {
        $command = "dev wifi connect " . escapeshellarg($ssid);
        
        if ($password) {
            $command .= " password " . escapeshellarg($password);
        }
        
        if ($device) {
            $command .= " ifname " . escapeshellarg($device);
        }
        
        try {
            $this->executeCommand($command);
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Create WiFi hotspot
     */
    public function createHotspot($ssid, $password = null, $device = null)
    {
        $command = "dev wifi hotspot";
        
        if ($device) {
            $command .= " ifname " . escapeshellarg($device);
        }
        
        if ($ssid) {
            $command .= " con-name " . escapeshellarg($ssid);
        }
        
        if ($password) {
            $command .= " password " . escapeshellarg($password);
        }
        
        try {
            $this->executeCommand($command);
            return true;
        } catch (NmcliException $e) {
            return false;
        }
    }

    /**
     * Set whether to use sudo for commands
     */
    public function setUseSudo($useSudo)
    {
        $this->useSudo = $useSudo;
    }

    /**
     * Get whether sudo is being used
     */
    public function isUsingSudo()
    {
        return $this->useSudo;
    }
}