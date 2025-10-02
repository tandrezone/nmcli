<?php

namespace Tandrezone\NmcliPhp;

/**
 * Represents a single NetworkManager connection
 */
class Connection
{
    private $data;
    private $nmcli;

    /**
     * Constructor
     * 
     * @param array $data Connection data from nmcli
     * @param Nmcli $nmcli Reference to the main Nmcli instance
     */
    public function __construct(array $data, Nmcli $nmcli)
    {
        $this->data = $data;
        $this->nmcli = $nmcli;
    }

    /**
     * Get connection name
     */
    public function getName()
    {
        return $this->data['NAME'] ?? '';
    }

    /**
     * Get connection UUID
     */
    public function getUuid()
    {
        return $this->data['UUID'] ?? '';
    }

    /**
     * Get connection type
     */
    public function getType()
    {
        return $this->data['TYPE'] ?? '';
    }

    /**
     * Get connection device
     */
    public function getDevice()
    {
        return $this->data['DEVICE'] ?? '';
    }

    /**
     * Get connection state
     */
    public function getState()
    {
        return $this->data['STATE'] ?? '';
    }

    /**
     * Check if connection is active
     */
    public function isActive()
    {
        $state = strtolower($this->getState());
        return in_array($state, ['activated', 'connected', 'active']);
    }

    /**
     * Get specific connection property
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Bring this connection up
     */
    public function up()
    {
        return $this->nmcli->up($this->getName());
    }

    /**
     * Bring this connection down
     */
    public function down()
    {
        return $this->nmcli->down($this->getName());
    }

    /**
     * Modify this connection
     * 
     * @param array $options Key-value pairs of settings to modify
     */
    public function modify(array $options = [])
    {
        return $this->nmcli->modify($this->getName(), $options);
    }

    /**
     * Delete this connection
     */
    public function delete()
    {
        return $this->nmcli->delete($this->getName());
    }

    /**
     * Clone this connection
     * 
     * @param string $newName Name for the cloned connection
     */
    public function clone($newName)
    {
        return $this->nmcli->clone($this->getName(), $newName);
    }

    /**
     * Export this connection to a file
     * 
     * @param string $filename Path to export file
     */
    public function export($filename)
    {
        return $this->nmcli->export($this->getName(), $filename);
    }

    /**
     * Get edit command for this connection
     */
    public function getEditCommand()
    {
        return $this->nmcli->edit($this->getName());
    }

    /**
     * Show detailed information about this connection
     */
    public function show()
    {
        return $this->nmcli->show($this->getName());
    }

    /**
     * Reload this connection
     */
    public function reload()
    {
        // Reload all connections and then refresh this object's data
        $result = $this->nmcli->reload();
        if ($result) {
            $this->refresh();
        }
        return $result;
    }

    /**
     * Refresh this connection's data from nmcli
     */
    public function refresh()
    {
        $details = $this->nmcli->show($this->getName());
        if (!empty($details)) {
            $this->data = array_merge($this->data, $details[0] ?? []);
        }
        return $this;
    }

    /**
     * Convert to string representation
     */
    public function __toString()
    {
        return sprintf(
            "Connection[%s] Type: %s, Device: %s, State: %s",
            $this->getName(),
            $this->getType(),
            $this->getDevice(),
            $this->getState()
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
     * Check if connection has a specific property
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Magic getter for connection properties
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Magic isset for connection properties
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
}