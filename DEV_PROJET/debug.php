<?php
header('Content-Type: text/plain');
echo "Loaded Configuration File: " . php_ini_loaded_file() . "\n";
echo "mysqli loaded: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n";
echo "extension_dir: " . ini_get('extension_dir') . "\n";
print_r(get_loaded_extensions());
?>