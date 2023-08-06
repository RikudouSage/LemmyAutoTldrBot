<?php

namespace App\Command;

use App\Service\SiteHandlerCollection;
use App\SummaryProvider\SummaryProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:manual-run')]
final class ManualRunCommand extends Command
{
    public function __construct(
        private readonly SiteHandlerCollection $siteHandler,
        private readonly SummaryProvider $summaryProvider,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'url',
                mode: InputArgument::REQUIRED,
            )
            ->addOption(
                name: 'show-text',
                shortcut: 't',
                mode: InputOption::VALUE_NONE,
                description: 'Show the whole text instead of summary',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument('url');
        $content = $this->siteHandler->getContent($url);
        if (!$content) {
            $io->error('Failed getting content');

            return self::FAILURE;
        }

        if ($input->getOption('show-text')) {
            $io->success($content);
        } else {
            $summary = $this->summaryProvider->getSummary($content, 5);
            $io->success($summary);
        }

        return self::SUCCESS;
    }
}
