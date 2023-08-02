<?php

namespace App\SummaryProvider;

use Rikudou\MemoizeBundle\Attribute\Memoizable;
use Rikudou\MemoizeBundle\Attribute\Memoize;
use RuntimeException;

#[Memoizable]
final class SumySummaryProvider implements SummaryProvider
{
    #[Memoize]
    public function getSummary(string $text, int $sentences): array
    {
        $text = str_replace('"', '\"', $text);
        $script = __DIR__ . '/../../python/summarizer';
        $currentDir = getcwd() ?: throw new RuntimeException('Getting current directory failed');
        chdir(dirname($script));
        exec("{$script} \"{$text}\" {$sentences} 2>&1", $output, $exitCode);
        chdir($currentDir);
        if ($exitCode !== 0) {
            throw new RuntimeException('Could not get summary using the python script.');
        }

        return array_filter($output);
    }

    public function getId(): string
    {
        return 'sumy';
    }
}
