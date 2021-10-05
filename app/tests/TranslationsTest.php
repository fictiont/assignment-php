<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Translation;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TranslationsTest extends JWTApiTest
{
    /**
     * Test get empty collection
     */
    public function testGetInitialCollection(): void
    {
        //Check if authorisation needed
        $response = static::createClient()->request('GET', '/api/translations');
        $this->assertResponseStatusCodeSame('401');
        // Asserts that the returned content type is JSON
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $token = $this->getToken(array(
                'email' => 'test_readonly@gmail.com',
                'password' => 'testreadpassword'
        ));
        $client = static::createClientWithCredentials($token);
        $client->getKernelBrowser()->catchExceptions(false);
        $response = $client->request('GET', '/api/translations');
        $this->assertCount(4, $response->toArray());

        //Check if readonly works properly
        $this->expectException(AccessDeniedException::class);
        $response = $client->request(
            'POST',
            '/api/translations',
            ['body' => json_encode([
                    'language' => '/api/languages/eng',
                    'key' => '/api/keys/fd7eb701-5d03-4399-bec3-f4672e91d393',
                    'translation' => 'Test greetings'
            ])]
        );
        $this->assertResponseStatusCodeSame('403');
    }

    /**
     * Test new Translation creation
     */
    public function testCreateTranslation(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        $response = static::createClientWithCredentials()->request(
            'POST',
            '/api/translations',
            ['body' => json_encode([
                    'language' => '/api/languages/eng',
                    'key' => '/api/keys/fd7eb701-5d03-4399-bec3-f4672e91d393',
                    'translation' => 'Test greetings'
            ])]
        );
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'language' => '/api/languages/eng',
            'key' => '/api/keys/fd7eb701-5d03-4399-bec3-f4672e91d393',
            'translation' => 'Test greetings'
        ]);
        $this->assertMatchesResourceItemJsonSchema(Translation::class);
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
        $response = static::createClientWithCredentials($token)->request('GET', '/api/translations');

        $this->assertCount(5, $response->toArray());
    }

    /**
     * Test patch translation
     */
    public function testPatchTranslation(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        //find translation created
        $response = static::createClientWithCredentials($token)->request('GET', '/api/translations?key=fd7eb701-5d03-4399-bec3-f4672e91d393&language=eng');
        $foundTranslations = $response->toArray();
        $this->assertCount(1, $foundTranslations);
        $foundTranslation = $foundTranslations[0];

        $response = static::createClientWithCredentials($token, 'application/merge-patch+json')->request(
            'PATCH',
            '/api/translations/'.$foundTranslation['id'],
            ['body' => json_encode([
                'translation' => 'Greetings'
            ])]
        );
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'language' => $foundTranslation['language'],
            'key' => $foundTranslation['key'],
            'translation' => 'Greetings'
        ]);
    }

    /**
     * Deleting translation created during the test
     */
    public function testDeleteTranslation(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        //find translation created
        $response = static::createClientWithCredentials($token)->request('GET', '/api/translations?key=fd7eb701-5d03-4399-bec3-f4672e91d393&language=eng');
        $foundTranslations = $response->toArray();
        $this->assertCount(1, $foundTranslations);
        $foundTranslation = $foundTranslations[0];

        $response = static::createClientWithCredentials($token)->request(
            'DELETE',
            '/api/translations/'.$foundTranslation['id']
        );
        
        $this->assertResponseIsSuccessful();
    }
}
