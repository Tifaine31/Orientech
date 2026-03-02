<?php
$map = [
    "Ã©" => "é",
    "Ã¨" => "è",
    "Ãª" => "ê",
    "Ã " => "à",
    "Ã§" => "ç",
    "Ã´" => "ô",
    "Ã¹" => "ù",
    "Ã®" => "î",
    "Ã¯" => "ï",
    "Ã»" => "û",
    "Ã‰" => "É",
    "Ã€" => "À",
    "â€™" => "’",
    "â€“" => "–",
    "â€”" => "—",
    "â†" => "←",
    "â€¢" => "•",
    "âœ”" => "✔",
    "âœ–" => "✖",
    "Ã¼" => "ü",
    "Ã¶" => "ö",
    "Ã„" => "Ä",
    "Ã–" => "Ö",
    "Ãœ" => "Ü",
    "Ã¢" => "â",
    "Ã«" => "ë",
    "Ã±" => "ñ",
];
$dir = __DIR__ . '/../resources/views';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($rii as $file) {
    if ($file->isDir()) { continue; }
    $path = $file->getPathname();
    if (substr($path, -10) !== '.blade.php') { continue; }
    $content = file_get_contents($path);
    $updated = strtr($content, $map);
    if ($updated !== $content) {
        file_put_contents($path, $updated);
    }
}
echo "done";
