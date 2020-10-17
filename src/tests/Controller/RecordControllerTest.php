<?php


namespace App\Tests\Controller;


use App\Entity\Artist;
use App\Entity\Note;
use App\Entity\Record;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordControllerTest extends WebTestCase
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

    private function createUser(string $role): User
    {
        $user = (new User())
            ->setEmail($role . '@kritik.fr')
            ->setPseudo($role)
            ->setRoles([$role])
            ->setPassword('');

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }

    private function createRecord(): Record
    {
        $artiste = (new Artist())->setName('artiste');
        $record = (new Record())->setTitle('Titre')
                    ->setReleasedAt(new \DateTime())
                    ->setArtist($artiste);

        $this->manager->persist($artiste);
        $this->manager->persist($record);
        $this->manager->flush();

        return $record;
    }

    private function createNote(?User $user = null, ?Record $record = null): Note
    {
        $user ??= $this->createUser('ROLE_USER');
        $record ??= $this->createRecord();

        $note = (new Note())
            ->setRecord($record)
            ->setAuthor($user)
            ->setValue(5)
        ;

        $user->addNote($note);
        $record->addNote($note);

        $this->manager->persist($note);
        $this->manager->flush();

        return $note;
    }

    public function testAnonymousAccess()
    {
        //Création des entités utilisées pour le test
        $record = $this->createRecord();
        $uri =  sprintf('/record/%d', $record->getId());

        //Rechercher le formulaire de notation
        $crawler = $this->client->request('GET', $uri);
        $form = $crawler->filter('form[name="note_form"]')->getNode(0);
        self::assertNull($form);

        //Test avec un utilisateur connecté
        $this->client->loginUser($this->createUser('ROLE_USER'));
        $crawler = $this->client->request('GET', $uri);
        $form = $crawler->filter('form[name="note_form"]')->getNode(0);
        self::assertNotNull($form);
    }

    public function testCreateNote()
    {
        //Entités
        $user = $this->createUser('ROLE_USER');
        $record = $this->createRecord();

        //Connexion et requete
        $this->client->loginUser($user);
        $this->client->request('GET', sprintf('record/%d', $record->getId()));

        //Formulaire
        $crawler = $this->client->submitForm('Noter !', [
            'note_form[value]' => 5,
        ]);
        self::assertResponseIsSuccessful();

        //Message flash de succés
        $flashMessage = $crawler->filter('div.notification.is-success')->text();
        self::assertSame('Votre note a été enregistrée.', $flashMessage);
    }

    public function testDeleteNote()
    {
        //Entités
        $user = $this->createUser('ROLE_USER');
        $record = $this->createRecord();
        $note = $this->createNote($user, $record);

        $uri = sprintf('/record/%d', $record->getId());

        //Connexion et requete
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        //Rechercher le lien de suppression
        $this->client->click($crawler->filterXPath('descendant-or-self::a[i[@class="fas fa-times"]]')->link());
        self::assertResponseRedirects($uri);

        //suivre la redirection et vérifier la présence du message flash
        $this->client->followRedirect();
        self::assertSelectorExists('div.notification.is-info');
        self::assertSelectorTextSame('div.notification.is-info','Votre note a bien été supprimée.');
    }

}