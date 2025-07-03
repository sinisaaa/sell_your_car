<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ArticleCategory;
use App\Entity\ArticleCategoryField;
use App\Entity\ArticleManufacturer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportArticleManufacturersCommand extends Command
{

    /**
     * ImportArticleManufacturersCommand constructor.
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
        $this->setName('app:import-article-manufacturers')
            ->setDescription('Imports article manufacturers.')
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
        $alreadyImported = count($this->em->getRepository(ArticleManufacturer::class)->findAll()) > 0;
        if ($alreadyImported) {
            throw new Exception('Manufacturers already imported');
        }

        $manufacturersData = file_get_contents(__DIR__ . '/../../import/articleManufacturers.xml');

        if (false === $manufacturersData) {
            throw new Exception('Manufacturers can not be imported');
        }

        $manufacturers = new SimpleXMLElement($manufacturersData);

        foreach ($manufacturers as $manufacturer) {
            $id = (int)$manufacturer->column[0];
            $name = (string)$manufacturer->column[1];
            $categoryName = (string)$manufacturer->column[2];
            $order = (int)$manufacturer->column[3];

            $category = $this->em->getRepository(ArticleCategory::class)->findOneBy(['name' => $categoryName]);

            if (null === $category) {
                throw new Exception('Article category not found');
            }

            $articleManufacturer = new ArticleManufacturer();
            $articleManufacturer->setId($id)
                ->setName($name)
                ->setCategory($category)
                ->setOrderCategory($order);

            $this->em->persist($articleManufacturer);
        }

        $this->em->flush();

        $output->writeln('<fg=green>Article manufacturers successfully imported!</>');

        return Command::SUCCESS;
    }

}