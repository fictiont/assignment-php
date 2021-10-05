<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Key;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class KeysTest extends JWTApiTest
{
    /**
     * Test get empty collection
     */
    public function testGetInitialCollection(): void
    {
        //Check if authorisation needed
        $response = static::createClient()->request('GET', '/api/keys');
        $this->assertResponseStatusCodeSame('401');
        // Asserts that the returned content type is JSON
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $token = $this->getToken(array(
                'email' => 'test_readonly@gmail.com',
                'password' => 'testreadpassword'
        ));
        $client = static::createClientWithCredentials($token);
        $client->getKernelBrowser()->catchExceptions(false);
        
        $response = $client->request('GET', '/api/keys');
        $this->assertCount(2, $response->toArray());

        //Check if readonly works properly
        $this->expectException(AccessDeniedException::class);
        $response = $client->request(
            'POST',
            '/api/keys',
            ['body' => json_encode([
                    'keyCode' => 'test.key',
                    'description' => 'test description'
            ])]
        );
        $this->assertResponseStatusCodeSame('403');
    }

    /**
     * Test new Key creation
     */
    public function testCreateKey(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        $response = static::createClientWithCredentials()->request(
            'POST',
            '/api/keys',
            ['body' => json_encode([
                    'keyCode' => 'test.key',
                    'description' => 'test description'
            ])]
        );
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'keyCode' => 'test.key',
            'description' => 'test description'
        ]);
        $this->assertMatchesResourceItemJsonSchema(Key::class);
    }

    /**
     * Test get not empty collection
     */
    public function testGetCollection(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        $response = static::createClientWithCredentials($token)->request('GET', '/api/keys');

        $this->assertCount(3, $response->toArray());
    }

    /**
     * Test patch translation
     */
    public function testPatchKey(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        //find key created
        $response = static::createClientWithCredentials($token)->request('GET', '/api/keys?keyCode=test.key');
        $foundKeys = $response->toArray();
        $this->assertCount(1, $foundKeys);
        $foundKey = $foundKeys[0];

        $response = static::createClientWithCredentials($token, 'application/merge-patch+json')->request(
            'PATCH',
            '/api/keys/'.$foundKey['id'],
            ['body' => json_encode([
                'description' => 'Description patched'
            ])]
        );
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'keyCode' => $foundKey['keyCode'],
            'description' => 'Description patched'
        ]);
    }

    /**
     * Deleting translation created during the test
     */
    public function testDeleteKey(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        //find key created
        $response = static::createClientWithCredentials($token)->request('GET', '/api/keys?keyCode=test.key');
        $foundKeys = $response->toArray();
        $this->assertCount(1, $foundKeys);
        $foundKey = $foundKeys[0];

        $response = static::createClientWithCredentials($token)->request(
            'DELETE',
            '/api/keys/'.$foundKey['id']
        );
        
        $this->assertResponseIsSuccessful();
    }
}
