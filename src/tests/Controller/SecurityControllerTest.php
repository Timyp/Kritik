<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Extension\DataCollector\FormDataCollector;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Vérifier le fonctionnement de l'inscription
     * @dataProvider provideUserData
     */
    public function testRegister(
        bool $resultat,
        string $email,
        string $pseudo,
        string $password,
        string $confirmation,
        int $expectedErrorCount = 0
    ){
        $client = static::createClient();
        $client->request('GET', '/register');

        //La page est accessible
        self::assertResponseIsSuccessful();

        //Activer le profiler pour pouvoir récupérer le nombre d'erreur de formulaire
        $client->enableProfiler();

        //Remplir et envoyer le formulaire
        $client->submitForm('Inscription', [
            'user_registration_form[email]'                 => $email,
            'user_registration_form[pseudo]'                => $pseudo,
            'user_registration_form[plainPassword][first]'  => $password,
            'user_registration_form[plainPassword][second]' => $confirmation,
        ]);

        //Récuprer le nombre d"erreurs de formulaire
        /** @var FormDataCollector $formCollector */
        $formCollector = $client->getProfile()->getCollector('form');
        $errorCount = $formCollector->getData()['nb_errors'];

        if($resultat == true) {
            self::assertResponseRedirects('/login');
            self::assertSame(0, $errorCount);
        } else {
            self::assertResponseIsSuccessful();
            self::assertSame($expectedErrorCount, $errorCount);
        }
    }

    /**
     * Data provider: fourni des données de formulaire d'inscription pour la méthode testRegister()
     */
    public function provideUserData()
    {
        return [
            [
                'resultat' => true,
                'email' => 'toto@gmail.com',
                'pseudo' => 'toto',
                'password' => 'P@ssw@rd',
                'confirmation' => 'P@ssw@rd',
            ],
            [
                'resultat' => false,
                'email' => 'toto@@gmail.com',
                'pseudo' => '!tot@o',
                'password' => 'P@ssw@rd',
                'confirmation' => '123',
                'errors' => 3,
            ],
            [
                'resultat' => false,
                'email' => '',
                'pseudo' => '',
                'password' => '',
                'confirmation' => '',
                'errors' => 3,
            ],
            [
                'resultat' => false,
                'email' => 'toto@gmail.com',
                'pseudo' => 't',
                'password' => 'p',
                'confirmation' => 'p',
                'errors' => 2,
            ],
        ];
    }
}