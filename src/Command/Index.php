<?php

declare(strict_types=1);

namespace Gally\SyliusPlugin\Command;

use Gally\SyliusPlugin\Indexer\AbstractIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Index shopware entities data to Gally.
 */
class Index extends Command
{
    protected static $defaultName = 'gally:index';

    /**
     * @param AbstractIndexer[] $indexers
     */
    public function __construct(
        private iterable $indexers
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Index taxon and product entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("");
        foreach ($this->indexers as $indexer) {
            $time = microtime(true);
            $message = "<comment>Indexing {$indexer->getEntityType()}</comment>";
            $output->writeln("$message ...");
            try {
                $indexer->reindex();
            } catch (\Exception $e) {
                $output->writeln($e->getTraceAsString());
            }
            $time = number_format(microtime(true) - $time, 2);
            $output->writeln("\033[1A$message <info>✔</info> ($time)s");
        }
        $output->writeln("");

        return 0;
    }
}
