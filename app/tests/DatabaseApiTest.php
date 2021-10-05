<?php
namespace App\Tests;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

/**
 * Helper which providing methods to work with JWT tokens in requests
 */
abstract class JWTApiTest extends ApiTestCase {
    /**
     * storing lastly created token
     */
    private $token = null;

    /**
     * Create client with token in headers.
     * @return Client client for running requests
    */
    protected function createClientWithCredentials($token = null, $contentType = null): \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client
    {
        $token = $token ?: $this->getToken();
        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token, 'Content-Type' => $contentType ?: 'application/json', 'Accept' => 'application/json']]);
    }

    /**
     * Retrieve jwt token.
     * @return string token
    */
    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient([], ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json']])->request('POST', '/api/authenticate', ['body' => json_encode($body ?: [
            'email' => 'test_readonly@gmail.com',
            'password' => 'testreadpassword',
        ])]);
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->token = $data->token;

        return $data->token;
    }
}
