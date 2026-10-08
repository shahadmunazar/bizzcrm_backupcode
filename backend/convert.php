<?php
foreach (['active.txt', 'commented.txt', 'target.txt', 'output.json'] as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        // UTF-16LE conversion
        $utf8 = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
        // Let's strip BOM if it exists
        if (str_starts_with($utf8, "\xEF\xBB\xBF")) {
            $utf8 = substr($utf8, 3);
        } else if (str_starts_with($content, "\xFF\xFE")) {
            // mb_convert_encoding might keep the BOM or change it. Let's check:
            if (str_starts_with($utf8, "\xFE\xFF") || str_starts_with($utf8, "\xFF\xFE")) {
                $utf8 = substr($utf8, 2);
            }
        }
        file_put_contents($f . '.utf8', $utf8);
    }
}
echo "Done converting!\n";
