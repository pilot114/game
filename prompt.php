<?php

include_once './vendor/autoload.php';

/** @return Generator<string, string> */
function readAllFiles(string $dir): \Generator
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            continue;
        }
        yield $file->getPathname() => file_get_contents($file->getPathname());
    }
}

echo "Исходный код проекта:\n";
foreach (readAllFiles('src') as $name => $content) {
    echo "\n\n### файл $name\n\n";
    echo($content);
}
echo "\n\nДавай улучшим и расширим этот код\n";
