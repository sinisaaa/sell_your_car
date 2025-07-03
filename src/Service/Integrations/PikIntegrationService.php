<?php

declare(strict_types=1);

namespace App\Service\Integrations;

use App\Entity\Article;
use App\Entity\ArticleArticleCategoryField;
use App\Entity\ArticleCategory;
use App\Entity\ArticleCategoryField;
use App\Entity\ArticleCategoryFieldOption;
use App\Entity\ArticleImage;
use App\Entity\ArticleManufacturer;
use App\Entity\ArticleManufacturerModel;
use App\Entity\Location;
use App\Entity\User;
use App\Helper\Exceptions\PikException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PikIntegrationService
{

    /**
     * PikIntegrationService constructor.
     * @param EntityManagerInterface $em
     * @param PikCommunicationService $pikCommunicationService
     */
    public function __construct(
        private EntityManagerInterface $em,
        private PikCommunicationService $pikCommunicationService
    )
    {
    }

    /**
     * @throws GuzzleException
     * @throws PikException
     * @throws \JsonException
     */
    public function syncArticles(): void
    {
        $users = $this->em->getRepository(User::class)->findAllFromPik();

        foreach ($users as $user) {
            $articlesFromPik = array_column($this->em->getRepository(Article::class)->findAllFromPikIdsByUser($user), 'pikId');
            $this->syncArticlesForUser($user, 1, $articlesFromPik);
        }
    }

    /**
     * @param User $user
     * @param int $page
     * @param Article[] $existingUserArticles
     *
     * @throws PikException
     * @throws GuzzleException
     * @throws \JsonException
     */
    private function syncArticlesForUser(User $user, int $page, array $existingUserArticles = []): void
    {
        while(true) {
            $pikUserArticlesResponse = $this->pikCommunicationService->fetchUserArticles($user, $page);

            if (isset($pikUserArticlesResponse['success']) && true === $pikUserArticlesResponse['success']) {
                $pikArticles = $pikUserArticlesResponse['artikli'];

                if (empty($pikArticles)) {
                    break;
                }

                foreach ($pikArticles as $pikArticle) {
                    if (in_array((string)$pikArticle['id'], $existingUserArticles, true)) {
                        continue;
                    }

                    $pikArticleDetailResponse = $this->pikCommunicationService->fetchArticleDetails($pikArticle['id']);
                    if (isset($pikArticleDetailResponse['success']) && true === $pikArticleDetailResponse['success']) {
                        $articleDetails = $this->getArticleDetails($pikArticleDetailResponse['artikal']);

                        $article = $this->insertArticle($articleDetails['basicData'], $user);
                        $this->insertArticleFields($article, $articleDetails['basicData']['category'], $articleDetails['articleFields']);
                        $this->insertImages($articleDetails['images'], $article);
                    }
                }
            }
            ++$page;
        }
    }

    /**
     * @param array $article
     * @return array
     */
    private function getArticleDetails(array $article): array
    {
        $basicData = $this->getArticleBasicData($article);
        $images = $article['slike'];

        return [
            'basicData' => $basicData,
            'images' => $images,
            'articleFields' => $article['osobine']
        ];
    }

    /**
     * @param $article
     * @return array
     */
    public function getArticleBasicData($article): array
    {
        $pik_id = (int)$article['id'];
        $title =  $article['naslov'];
        $urgent = isset($article['hitno']) && $article['hitno'];
        $price = is_numeric(explode(' ', $article['cijena'])[0]) ? (float)str_replace('.', '', explode(' ', $article['cijena'])[0]) : 0;
        $exchange = isset($article['zamjena']) && !empty($article['zamjena']);
        $location = isset($article['grad']) ? $this->em->getRepository(Location::class)->findOneBy(['city' => $article['grad']]) : null;
        $negotiable = $price == 0;
        $description = isset($article['detaljni_opis']) ? strip_tags($article['detaljni_opis']) : null;
        $manufacturer = isset($article['proizvodjac']['naziv']) ? $this->em->getRepository(ArticleManufacturer::class)->findOneBy(['name' => $article['proizvodjac']['naziv']]) : null;
        $manufacturerModel = isset($article['model']['naziv']) ? $this->em->getRepository(ArticleManufacturerModel::class)->findOneBy(['name' => $article['model']['naziv']]) : null;
        $category = isset($article['putanja']) ? $this->getArticleCategory($article['putanja']) : null;
        $telephone = $article['korisnik']['pravna_tel'] ?? null;
        $conditions = isset($article['novo']) && $article['novo'] ? 'Novo' : 'Polovno';

        return [
            'pikId' => $pik_id, 'title' => $title, 'urgent' => $urgent, 'price' => $price, 'exchange' => $exchange,
            'location' => $location, 'description' => $description, 'telephone' => $telephone,
            'category' => $category, 'manufacturer' => $manufacturer, 'manufacturerModel' => $manufacturerModel, 'negotiable' => $negotiable,
            'conditions' => $conditions
        ];
    }

    /**
     * @param array $category
     * @return ArticleCategory|null
     */
    private function getArticleCategory(array $category): ?ArticleCategory
    {
        if ($category[1]['naziv'] === 'Automobili') {
            return $this->em->getRepository(ArticleCategory::class)->findOneBy(['name' => 'cars']);
        }
        if ($category[1]['naziv'] === 'Motocikli') {
            return $this->em->getRepository(ArticleCategory::class)->findOneBy(['name' => 'motorcycles']);
        }
        if ($category[1]['naziv'] === 'Teretna vozila' || $category[1]['naziv'] === 'Autobusi i minibusi') {
            return $this->em->getRepository(ArticleCategory::class)->findOneBy(['name' => 'trucks']);
        }
        if (count($category) > 2 && $category[4]['naziv'] === 'Felge') {
            return $this->em->getRepository(ArticleCategory::class)->findOneBy(['name' => 'wheels']);
        }

        return null;
    }

    /**
     * @param mixed $articleDetails
     * @param User $user
     * @return Article
     */
    private function insertArticle(mixed $articleDetails, User $user): Article
    {
        $article = new Article();
        $article->setUser($user)
            ->setTitle($articleDetails['title'])
            ->setDescription($articleDetails['description'])
            ->setTelephone($articleDetails['telephone'])
            ->setCategory($articleDetails['category'])
            ->setManufacturer($articleDetails['manufacturer'])
            ->setManufacturerModel($articleDetails['manufacturerModel'])
            ->setConditions($articleDetails['conditions'])
            ->setUrgent($articleDetails['urgent'])
            ->setPrice($articleDetails['price'])
            ->setLocation($articleDetails['location'])
            ->setExchange($articleDetails['exchange'])
            ->setNegotiable($articleDetails['negotiable'])
            ->setPikId($articleDetails['pikId'])
            ->setIsDraft(false)
            ->setCreatedAt(new \DateTime());

        $this->em->persist($article);
        $this->em->flush();

        return $article;
    }

    /**
     * @param Article $article
     * @param ArticleCategory|null $category
     * @param array $articleFields
     */
    public function insertArticleFields(Article $article, ?ArticleCategory $category, array $articleFields): void
    {
        if (null === $category) {
            return;
        }

        foreach ($articleFields as $articleField) {
            $fieldName = $this->mapFieldNames($articleField['naziv'], $category->getName());

            /** @var ArticleCategoryField $articleFieldObject */
            $articleFieldObject = $this->em->getRepository(ArticleCategoryField::class)->findOneBy(
                ['category' => $category, 'name' => $fieldName]
            );

            if (null !== $articleFieldObject) {
                $value = $this->adjustValue($articleField['vrijednost']);

                if ($articleFieldObject->isOptionsField()) {
                    $articleFieldOption = $this->em->getRepository(ArticleCategoryFieldOption::class)->findOneBy(
                        ['field' => $articleFieldObject, 'name' => $value]
                    );

                    if (null !== $articleFieldOption) {
                        $articleArticleField = new ArticleArticleCategoryField();
                        $articleArticleField->setArticle($article);
                        $articleArticleField->setField($articleFieldObject);
                        $articleArticleField->addFieldOption($articleFieldOption);

                        $this->em->persist($articleArticleField);
                    }

                } else {
                    $articleArticleField = new ArticleArticleCategoryField();
                    $articleArticleField->setArticle($article);
                    $articleArticleField->setField($articleFieldObject);
                    $articleArticleField->setValue($value);

                    $this->em->persist($articleArticleField);
                }
            }
        }

        $this->em->flush();
    }

    /**
     * @param string $key
     * @param string $category
     * @return string
     */
    public function mapFieldNames(string $key, string $category): string
    {
        if ($category === 'cars') {
            switch ($key) {
                case 'Godište':
                    return 'Godište';
                case 'Kilometraža':
                    return 'Kilometraža';
                case 'Gorivo':
                    return 'Gorivo';
                case 'Kilovata (KW)':
                    return 'Kilovati';
                case 'Konjska Snaga':
                    return 'Konjska Snaga';
                case 'Kubikaža':
                    return 'Kubikaža ccm';
                case 'Tip':
                    return 'Karoserija';
                case 'Pogon':
                    return 'Pogon';
                case 'Emisioni standard':
                    return 'Emisioni Standard';
                case 'Transmisija':
                    return 'Mjenjač';
                case 'Broj stepeni prijenosa':
                    return 'Broj brzina mjenjača';
                case 'Broj vrata':
                    return 'Broj vrata';
                case 'Boja':
                    return 'Boja spoljašnjosti';
                case 'Klima':
                    return 'Klima';
            }
        }
        if ($category === 'motorcycles') {
            switch ($key) {
                case 'Godište':
                    return 'Godište';
                case 'Kilometraža':
                    return 'Kilometraža';
            }
        }
        if ($category === 'trucks') {
            switch ($key) {
                case 'Godište':
                    return 'Godište';
                case 'Kilometraža':
                    return 'Kilometraža';
                case 'Gorivo':
                    return 'Gorivo';
                case 'Kilovata (KW)':
                    return 'Kilovati';
                case 'Konjskih snaga':
                    return 'Konjska Snaga';
                case 'Kubikaža':
                    return 'Kubikaža ccm';
                case 'Emisioni standard':
                    return 'Emisioni Standard';
                case 'Broj stepeni prijenosa':
                    return 'Broj brzina mjenjača';
                case 'Broj osovina':
                    return 'Broj Osovina';
                case 'Boja':
                    return 'Boja spoljašnjosti';
            }
        }
        if ($category === 'wheels') {
            switch ($key) {
                case 'Materijal':
                    return 'Materijal';
                case 'Veličina (inch)':
                    return 'Veličina';
                case 'Raspon šarafa':
                    return 'Promjer rupa';
            }
        }

        return '';
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function adjustValue(mixed $value): mixed
    {
        if (strpos($value, '/')) {
            if (is_numeric(substr($value, -1))) {
                return substr($value, -1);
            }

            return $value;
        }
        if (strpos($value, '.')) {
            if (strlen($value) > 3) {
                return str_replace('.', '', $value);
            }

            return (int) str_replace('.', '', $value) * 100;
        }
        if (strpos($value, '+')) {
            return substr($value, 0, 1);
        }

        return $value;
    }


    /**
     * @param array $images
     * @param Article $article
     */
    private function insertImages(array $images, Article $article): void
    {
        foreach($images as $key => $image) {
            $newFile = __DIR__.'/../../../import/files/test.jpg';

            copy($image . '-velika.jpg', $newFile);
            $mimeType = mime_content_type($newFile);
            $finalName = md5(uniqid((string)mt_rand(), true)).".jpg";

            $imageFile = new UploadedFile($newFile, $finalName, $mimeType, null, true);
            $imageDimension = getimagesize($imageFile->getRealPath());

            $articleImage = new ArticleImage();
            $articleImage->setArticle($article);
            $articleImage->setImageFile($imageFile);
            $articleImage->setWidth($imageDimension[0] ?? null);
            $articleImage->setHeight($imageDimension[1] ?? null);
            $articleImage->setImageOrder($key);
            $articleImage->setExtension($imageFile->getClientOriginalExtension());
            $articleImage->setCreatedAt(new DateTime());

            $this->em->persist($articleImage);
        }

        $this->em->flush();
    }

}