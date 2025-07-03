<?php

declare(strict_types=1);

namespace App\Tests\API\Role\AutoComplete;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class RoleAutoCompleteAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testAutoCompleteSuccess(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'GET',
            '/api/roles/autocomplete?q=Admin'
        );

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
    }

    /**
     *
     */
    public function testAutoCompleteSFailedNotLoggedIn(): void
    {
        $this->client->request(
            'GET',
            '/api/roles/autocomplete?q=Admin'
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }


}