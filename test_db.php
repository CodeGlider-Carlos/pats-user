<?php
$files = ['pats_bd.sql', 'dcommx1_ezsystem_pats (6).sql'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "Searching in $file...\n";
        $handle = fopen($file, "r");
        if ($handle) {
            $count = 0;
            while (($line = fgets($handle)) !== false) {
                if (stripos($line, 'INSERT INTO `pats_cat_proveedores`') !== false || stripos($line, 'INSERT INTO pats_cat_proveedores') !== false) {
                    echo substr($line, 0, 500) . "...\n";
                    $count++;
                    if ($count > 5) break;
                }
            }
            fclose($handle);
            if ($count === 0) {
                echo "No inserts found for pats_cat_proveedores.\n";
            }
        }
    } else {
        echo "File $file does not exist.\n";
    }
}
