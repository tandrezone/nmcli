<?php

namespace Tandrezone\NmcliPhp\Tests;

use PHPUnit\Framework\TestCase;
use Tandrezone\NmcliPhp\Nmcli;
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
    }

    public function testGetDevices()
    {
        // This test would require nmcli to be available
        // In a real test environment, you might mock the exec function
        $devices = $this->nmcli->getDevices();
        $this->assertIsArray($devices);
    }

    public function testEditReturnsCommand()
    {
        $command = $this->nmcli->edit('test-connection');
        echo "command: $command\n";
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
}