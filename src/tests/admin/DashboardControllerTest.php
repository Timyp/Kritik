<?php


namespace App\Tests\admin;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private ?EntityManagerInterface $manager;

    /**
     * Méthode executé avant chaque test
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = $this->client->getContainer()->get('doctrine')->getManager();
    }


    /**
     * Méthode executée après chaque test
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->manager->close();
        $this->manager = null;
    }

    /**
     * Créer un utilisateur
     */
    private function createUser(string $role): User
    {

        $admin = (new User())
            ->setEmail($role . '@kritik.fr')
            ->setPseudo($role)
            ->setRoles([$role])
            ->setPassword('');

        $this->manager->persist($admin);
        $this->manager->flush();

        return $admin;
    }

    /**
     * Liste d'utilisateur avec différents roles
     */
    public function provideUsers(): \Generator
    {
        $roles = ['ROLE_USER', 'ROLE_EDITOR', 'ROLE_ADMIN'];
        foreach ($roles as $role) {
            yield [
                'resultat' => in_array($role, ['ROLE_ADMIN', 'ROLE_EDITOR']),
                'role' => $role,
            ];
        }
    }

    /**
     * Vérifier les droits d'accès au back office
     * @dataProvider provideUsers
     */
    public function testDashboardAccess(bool $resultat, string $role)
    {
        //On en crée pas le client, voir la méthode setup
        $user= $this->createUser($role);

        //On se connecte avec un utilisateur
        $this->client->loginUser($user);

        //On accede au back-office
        $this->client->request('GET', '/admin');

        if($resultat === true) {
            self::assertResponseIsSuccessful();
        } else {
            self::assertResponseStatusCodeSame(403);
        }
    }

    /**
     * Vérifier l'accès au back office pour les utilistateurs anonymes
     */
    public function testAnonymousDashboardAccess()
    {
        $this->client->request('GET', '/admin');
        self::assertResponseRedirects('/login');
    }
}