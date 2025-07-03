<?php

declare(strict_types=1);

namespace App\Tests\API\MyProfile\ChangePassword;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class MyProfileChangePasswordAPITest extends AbstractAPITestCase
{

    /**
     * @return array
     */
    public function formDataValidationErrors_DP(): array
    {
        return [
            [['oldPassword' => '123qweQWE', 'password' => ['first' => '123qweQWE', 'second' => '123qweQWE1']], Response::HTTP_BAD_REQUEST, 'Passwords not match'],
            [['oldPassword' => '123qweQWE', 'password' => ['first' => 'qweQWEEE', 'second' => 'qweQWEEE']], Response::HTTP_BAD_REQUEST, 'Passwords without number'],
            [['oldPassword' => '123qweQWE', 'password' => ['first' => '123qweee', 'second' => '123qweee']], Response::HTTP_BAD_REQUEST, 'Passwords without uppercase'],
            [['oldPassword' => '123qweQWE', 'password' => ['first' => '123QWEEE', 'second' => '123QWEEE']], Response::HTTP_BAD_REQUEST, 'Passwords without lowercase'],
            [['oldPassword' => '123qweQWE', 'password' => ['first' => '123QWqw', 'second' => '123QWqw']], Response::HTTP_BAD_REQUEST, 'Passwords less than 8 characters'],
            [['oldPassword' => '123qweQWE', 'password' => []], Response::HTTP_BAD_REQUEST, 'Empty password'],
            [['oldPassword' => '123qweQWE', 'password' => ['first' => '123qweQWE']], Response::HTTP_BAD_REQUEST, 'Empty repeated password'],
            [['oldPassword' => '123qweQWE', 'password' => ['second' => '123qweQWE']], Response::HTTP_BAD_REQUEST, 'Empty repeated password'],
            [['password' => ['first' => '123qweQWE', 'second' => '123qweQWE']], Response::HTTP_BAD_REQUEST, 'No old password'],
            [['oldPassword' => 'invalid', 'password' => ['first' => '123qweQWE', 'second' => '123qweQWE']], Response::HTTP_CONFLICT, 'Invalid old password'],

        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testChangePasswordFailedValidationErrors(mixed $passwordData, int $statusCode): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'POST',
            '/api/my-profile/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                $passwordData
            , JSON_THROW_ON_ERROR)
        );

        self::assertSame($statusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testChangePasswordSuccess(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'POST',
            '/api/my-profile/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'oldPassword' => '123qweQWE',
                'password' => ['first' => '123qweQWE', 'second' => '123qweQWE']
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

}