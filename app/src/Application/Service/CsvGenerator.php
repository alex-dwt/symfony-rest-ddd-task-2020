<?php

declare(strict_types=1);

namespace App\Application\Service;

class CsvGenerator
{
    // todo rewrite
    public function execute(array $data): string
    {
        $contents = '';

        $file = fopen('php://temp', 'r+');
        foreach ($data as $line) {
            fputcsv($file, $line);
        }

        rewind($file);

        while (!feof($file)) {
            $contents .= fread($file, 8192);
        }
        fclose($file);

        return $contents;
    }
}
