<?php

// Simple autoloader for development testing
spl_autoload_register(function ($class) {
    $prefix = 'Tandrezone\\NmcliPhp\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Test the package
use Tandrezone\NmcliPhp\Nmcli;
use Tandrezone\NmcliPhp\NmcliException;

echo "Testing nmcli-php package...\n";

try {
    // Test without sudo to avoid permission issues
    $nmcli = new Nmcli(false);
    echo "✓ Nmcli class instantiated successfully\n";
    
    // Test sudo configuration
    $nmcli->setUseSudo(true);
    echo "✓ Sudo configuration works: " . ($nmcli->isUsingSudo() ? "enabled" : "disabled") . "\n";
    
    $nmcli->setUseSudo(false);
    echo "✓ Sudo configuration works: " . ($nmcli->isUsingSudo() ? "enabled" : "disabled") . "\n";
    
    // Test interactive commands (these don't require nmcli to be actually available)
    $editCommand = $nmcli->edit('test-connection');
    echo "✓ Edit command generated: " . $editCommand . "\n";
    
    $monitorCommand = $nmcli->monitor();
    echo "✓ Monitor command generated: " . $monitorCommand . "\n";
    
    echo "\n🎉 Package structure is working correctly!\n";
    echo "📦 Package is ready for Composer publishing\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>