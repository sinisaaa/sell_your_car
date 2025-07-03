<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ArticleCategory;
use App\Entity\ArticleCategoryField;
use App\Entity\ArticleManufacturer;
use App\Entity\ArticleManufacturerModel;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportArticleManufacturerModelsCommand extends Command
{

    /**
     * ImportArticleManufacturerModelsCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('app:import-article-manufacturer-models')
            ->setDescription('Imports article manufacturer models.')
            ->setHidden(true);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $alreadyImported = count($this->em->getRepository(ArticleManufacturerModel::class)->findAll()) > 0;
        if ($alreadyImported) {
            throw new Exception('Manufacturer models already imported');
        }

        $manufacturerModelsData = file_get_contents(__DIR__ . '/../../import/articleManufacturerModels.xml');

        if (false === $manufacturerModelsData) {
            throw new Exception('Manufacturer models can not be imported');
        }

        $manufacturerModels = new SimpleXMLElement($manufacturerModelsData);

        foreach ($manufacturerModels as $manufacturerModel) {
            $id = (int)$manufacturerModel->column[0];
            $name = (string)$manufacturerModel->column[1];
            $manufacturerId = (int)$manufacturerModel->column[2];

            $manufacturer = $this->em->getRepository(ArticleManufacturer::class)->find($manufacturerId);

            if (null === $manufacturer) {
                throw new Exception('Article manufacturer not found');
            }

            $articleManufacturerModel = new ArticleManufacturerModel();
            $articleManufacturerModel->setId($id)
                ->setName($name)
                ->setManufacturer($manufacturer);

            $this->em->persist($articleManufacturerModel);
        }

        $this->em->flush();

        $output->writeln('<fg=green>Article manufacturer models successfully imported!</>');

        return Command::SUCCESS;
    }

}