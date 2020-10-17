<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Les classes de test sont suffixées "test"
 * Les tests fonctionnels héritent de WebTestCase
 */
class HomeControllerTest extends WebTestCase
{
    /**
     * Vérifier si la page d'accueil est accessible
     * Les méthodes de test sont publiques et préfixées de "test"
     * Une méthode égale un test
    */
    public function testHomePage()
    {
        //Création d'un client web
        $client = static::CreateClient();

        // Effectue une requête vers la page d'accueil
        // Le $crawler est à créer uniquement si on veut manipuler la page
        $crawler = $client->request('GET', '/');

        //Il faut faire des assertions (=affirmation)
        //Sans insertion un test est "Risky"
        self::assertResponseIsSuccessful();  //réponse http 2xx
        $title = $crawler->filter('body > div.section > h1.title')->text();
        self::assertEquals('Accueil', $title);
    }

}