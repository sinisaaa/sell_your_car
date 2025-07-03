<?php

declare(strict_types=1);

namespace App\Service\Integrations;

use App\Entity\User;
use App\Helper\Exceptions\PikException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class PikCommunicationService
{

    public const PIK_BASE_URI = 'https://api.bik.com';
    public const PIK_USER_ARTICLES_URL = '/aricles/?username={{username}}&page={{page}}';
    public const PIK_ARTICLE_DETAILS_URL = '/articles/{{id}}';

    /**
     * PikCommunicationService constructor.
     */
    public function __construct()
    {
        $config = [
            'base_uri' => self::PIK_BASE_URI,
            'http_errors' => false
        ];

        $this->httpClient = new Client($config);
    }

    /**
     * @param User $user
     * @param int $page
     * @return ResponseInterface
     *
     * @throws GuzzleException
     * @throws PikException
     * @throws \JsonException
     */
    public function fetchUserArticles(User $user, int $page = 1): mixed
    {
        $pikResponse = $this->sendRequest(
            str_replace(
                ['{{username}}', '{{page}}'],
                [$user->getPikName(), $page],
                self::PIK_USER_ARTICLES_URL),
        );

        if ($pikResponse->getStatusCode() !== Response::HTTP_OK) {
            throw new PikException('Connection to PIK failed');
        }

        return json_decode((string)$pikResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param int $articleId
     * @return ResponseInterface
     *
     * @throws GuzzleException
     * @throws PikException
     * @throws \JsonException
     */
    public function fetchArticleDetails(int $articleId): mixed
    {
        $pikResponse = $this->sendRequest(
            str_replace(
                '{{id}}',
                (string)$articleId,
                self::PIK_ARTICLE_DETAILS_URL),
        );

        if ($pikResponse->getStatusCode() !== Response::HTTP_OK) {
            throw new PikException('Connection to PIK failed');
        }

        return json_decode((string)$pikResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $body
     * @param array $headers
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    private function sendRequest(
        string $path,
        string $method = 'GET',
        array $body = [],
        array $headers = []
    ): ResponseInterface
    {
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';

        $options = [
            'headers' => $headers,
            'form_params' => $body
        ];

        return $this->httpClient->request($method, $path, $options);
    }

}
