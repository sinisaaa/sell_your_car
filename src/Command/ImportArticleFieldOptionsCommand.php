<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ArticleCategoryField;
use App\Entity\ArticleCategoryFieldOption;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportArticleFieldOptionsCommand extends Command
{

    /**
     * ImportArticleFieldOptionsCommand constructor.
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
        $this->setName('app:import-article-field-options')
            ->setDescription('Imports article field options.')
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
        $alreadyImported = count($this->em->getRepository(ArticleCategoryFieldOption::class)->findAll()) > 0;
        if ($alreadyImported) {
            throw new Exception('Field options already imported');
        }

        $fieldOptionsData = file_get_contents(__DIR__ . '/../../import/articleFieldOptions.xml');

        if (false === $fieldOptionsData) {
            throw new Exception('Field options can not be imported');
        }

        $fieldOptions = new SimpleXMLElement($fieldOptionsData);

        foreach ($fieldOptions as $fieldOption) {
            $fieldId = (int)$fieldOption->column[1];
            $name = (string)$fieldOption->column[2];

            $field = $this->em->getRepository(ArticleCategoryField::class)->find($fieldId);

            if (null === $field) {
                throw new Exception('Attribute category field not found');
            }

            $articleCategoryFieldOption = new ArticleCategoryFieldOption();
            $articleCategoryFieldOption->setName($name)
                ->setField($field);

            $this->em->persist($articleCategoryFieldOption);
        }

        $this->em->flush();

        $output->writeln('<fg=green>Article category field options successfully imported!</>');

        return Command::SUCCESS;
    }

}