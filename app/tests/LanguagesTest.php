<?php
namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Language;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LanguagesTest extends JWTApiTest
{
    /**
     * Test get empty collection
     */
    public function testGetInitialCollection(): void
    {
        

        //Check if authorisation needed
        $response = static::createClient()->request('GET', '/api/languages');
        $this->assertResponseStatusCodeSame('401');

        // Asserts that the returned content type is JSON
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $token = $this->getToken(array(
                'email' => 'test_readonly@gmail.com',
                'password' => 'testreadpassword'
        ));
        $client = static::createClientWithCredentials($token);
        $client->getKernelBrowser()->catchExceptions(false);

        $response = $client->request('GET', '/api/languages');
        $this->assertCount(5, $response->toArray());

        //Check if readonly works properly
        $this->expectException(AccessDeniedException::class);
        $response = $client->request('POST', '/api/languages', ['body' => json_encode(['iso' => 'lit', 'name' => 'Lithuanian'])]);
        $this->assertResponseStatusCodeSame('403');
    }

    /**
     * Test new Language creation
     */
    public function testCreateLanguage(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        $response = static::createClientWithCredentials($token)->request('POST', '/api/languages', ['body' => json_encode(['iso' => 'lit', 'name' => 'Lithuanian'])]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'iso' => 'lit',
            'name' => 'Lithuanian'
        ]);
        $this->assertMatchesResourceItemJsonSchema(Language::class);
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
        $response = static::createClientWithCredentials($token)->request('GET', '/api/languages');

        $this->assertCount(6, $response->toArray());
    }

    /**
     * Test patch language
     */
    public function testPatchLanguage(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        $response = static::createClientWithCredentials($token, 'application/merge-patch+json')->request('PATCH', '/api/languages/lit', ['body' => json_encode(['name' => 'Lith'])]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'iso' => 'lit',
            'name' => 'Lith'
        ]);

        $this->assertMatchesResourceItemJsonSchema(Language::class);
    }

    /**
     * Deleting language created during the test
     */
    public function testDeleteLanguage(): void
    {
        $token = $this->getToken(array(
                'email' => 'test_full@gmail.com',
                'password' => 'testfullpassword'
        ));
        $response = static::createClientWithCredentials($token)->request('DELETE', '/api/languages/lit');
        
        $this->assertResponseIsSuccessful();
    }
}
