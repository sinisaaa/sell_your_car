<?php

declare(strict_types=1);

namespace App\Tests;

use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractAPITestCase extends WebTestCase
{

    /** @var KernelBrowser|null  */
    public ?KernelBrowser $client = null;

    /** @var string|null  */
    private ?string $token = null;

    /**
     *
     */
    public function setUp(): void
    {
        if (null === $this->client) {
            self::ensureKernelShutdown();
            $this->client = static::createClient();
        }
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @throws JsonException
     */
    public function loginAsStandardUser(
        string $username = 'user@mail.com',
        string $password = '123qweQWE',
    ): void
    {
        $this->sendLoginRequest(
            $username,
            $password
        );
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @throws JsonException
     */
    public function loginAsAdmin(
        string $username = 'admin@mail.com',
        string $password = '123qweQWE',
    ): void
    {
        $this->sendLoginRequest(
            $username,
            $password
        );
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @throws JsonException
     */
    public function loginAsCarDealer(
        string $username = 'carDealer@mail.com',
        string $password = '123qweQWE',
    ): void
    {
        $this->sendLoginRequest(
            $username,
            $password
        );
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @throws JsonException
     */
    private function sendLoginRequest(
        string $username,
        string $password
    ): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $username,
                'password' => $password,
            ], JSON_THROW_ON_ERROR)
        );

        $this->token = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['token'] ?? null;

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $this->token));
    }

    /**
     * @return UploadedFile
     */
    public function getImageForUpload(): UploadedFile
    {
        $testFilesFolder= __DIR__ . '/TestFiles';
        $testFiles = scandir($testFilesFolder);

        $defaultTestFileName = uniqid('', true).'car.jpeg';
        $defaultTestFilePath = $testFilesFolder . '/'. $defaultTestFileName;
        $currentTestFilePath = $testFilesFolder . '/' . $testFiles[2];

        copy($currentTestFilePath , $defaultTestFilePath);

        return new UploadedFile($currentTestFilePath, $currentTestFilePath, 'image/jpeg', null, true);
    }

    /**
     * @return UploadedFile
     */
    public function getInvalidImageForUpload(): UploadedFile
    {
        $testFilesFolder= __DIR__ . '/TestFiles/Documents';
        $testFiles = scandir($testFilesFolder);

        $currentTestFilePath = $testFilesFolder . '/' . $testFiles[2];

        return new UploadedFile($currentTestFilePath, $testFiles[2], 'application/pdf', null, true);
    }


}