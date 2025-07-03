<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ArticleCategory;
use App\Entity\ArticleCategoryField;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportArticleFieldsCommand extends Command
{

    /**
     * ImportArticleFieldsCommand constructor.
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
        $this->setName('app:import-article-fields')
            ->setDescription('Imports article fields.')
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
        $alreadyImported = count($this->em->getRepository(ArticleCategoryField::class)->findAll()) > 0;
        if ($alreadyImported) {
            throw new Exception('Fields already imported');
        }

        $fieldsData = file_get_contents(__DIR__ . '/../../import/articleFields.xml');

        if (false === $fieldsData) {
            throw new Exception('Fields can not be imported');
        }

        $fields = new SimpleXMLElement($fieldsData);

        foreach ($fields as $field) {
            $id = (string)$field->column[0];
            $name = (string)$field->column[1];
            $type = (string)$field->column[2];
            $categoryName = (string)$field->column[3];
            $required = (string)$field->column[4];
            $notes = (string)$field->column[5];
            $oldName = (string)$field->column[6];
            $attributeOrder = (string)$field->column[7];

            $category = $this->em->getRepository(ArticleCategory::class)->findOneBy(['name' => $categoryName]);

            if (null === $category) {
                throw new Exception('Article category not found');
            }

            $articleCategoryField = new ArticleCategoryField();
            $articleCategoryField->setId((int)$id)
                ->setName($name)
                ->setType($type)
                ->setCategory($category)
                ->setRequired((bool)$required)
                ->setNotes($notes)
                ->setOldName($oldName)
                ->setAttributeOrder((int)$attributeOrder);

            $this->em->persist($articleCategoryField);
        }

        $this->em->flush();

        $output->writeln('<fg=green>Article category fields successfully imported!</>');

        return Command::SUCCESS;
    }

}