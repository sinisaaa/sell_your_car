<?php

declare(strict_types=1);

namespace App\Command;

use App\Helper\Exceptions\PikException;
use App\Service\Integrations\PikIntegrationService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class SyncArticlesFromPikCommand extends Command
{

    /**
     * SyncArticlesFromPikCommand constructor.
     * @param PikIntegrationService $pikIntegrationService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private PikIntegrationService $pikIntegrationService,
        private EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('app:sync-articles-from-pik')
            ->setDescription('Syncs articles from Pik.ba\Olx.ba')
            ->setHelp('Syncs articles from Pik.ba\Olx.ba')
            ->setHidden(true);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->em->beginTransaction();
            $this->pikIntegrationService->syncArticles();
            $this->em->commit();
        } catch(Exception $e) {
            $output->writeln('<fg=red>Error occurred!</>');
            $output->writeln($e->getMessage());
            $this->em->rollback();

            return Command::FAILURE;
        }

        $output->writeln('<fg=green>Articles successfully synced!</>');

        return Command::SUCCESS;
    }

}