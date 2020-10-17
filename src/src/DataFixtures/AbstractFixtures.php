<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class AbstractFixtures extends Fixture
{
    private ObjectManager $manager;
    protected Generator $faker;
    /**
     * Créer plusieurs entités
     * @param int $count nombre d'entité à générer
     * @param string $groupName nom du groupe de référence
     * @param callable $factory function qui génère une entité
     */
    protected function createMany(int $count, string $groupName, callable $factory): void
    {
        for ($i = 0; $i < $count; $i++) {
            //La fonction doit retourner l'entité générée
            $entity = $factory($i);

            if ($entity === null) {
                throw new \LogicException('La fonction doit retourner l\'entité.');
            }

            //On prépare à l'enregistrement de l'entité
            $this->manager->persist($entity);

            //Enregistré l'entité avec une référence unique
            $reference = sprintf('%s_%d', $groupName, $i);
            $this->addReference($reference, $entity);
        }
    }

    /**
     * Récupérer une entité de manière aléatoire
     * @param string $groupName nom du groupe de référence dans lequel rechercher
     */
    protected function getRandomReference(string $groupName): object
    {
        if(false === isset($this->references[$groupName])){
            //Si on a pas déjà enregistré les références recherchées en propriété, on éxécute la manipulation
            $this->references[$groupName] = [];

            //On va parcourir la liste de toutes les références (tous les objet sont mélangé ensemble)
            //Le nom des références sont stocké dans les clés du tableau $key
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                //Si $key commence par $groupName, il s'agit d'un objet qu'on recherche
                if(strpos($key, $groupName . '_') === 0) {
                    //On enregistre la référence dans notre propriété
                    $this->references[$groupName][] = $key;
                }
            }

            //Si on a récupere aucune référence: erreur
            if([] === $this->references[$groupName]) {
                throw new \LogicException(sprintf('Aucune référence trouvée pour le groupe "%s"', $groupName));
            }
        }

        //On prend une référence aléatoire et on retourne l'objet associé
        $reference = $this->faker->randomElement($this->references[$groupName]);

        return $this->getReference($reference);
    }

    /**
     * @var array[] list des références enregistrées
     * chaque clé sera nommée par le groupe de références
     * cf mémoïsation
     */
    private array $references = [];

    /**
     * Méthode a implémenter pour générer les fausses données
     */
    abstract protected function loadData(): void;

    /**
     * Méthode appelé par le système de fixtures
     */
    public function load(ObjectManager $manager)
    {
        //Enregitrement du manager et initialisation de Faker
        $this->manager = $manager;
        $this->faker = Factory::create('fr_FR');

        //Génération et enregistrement en base des données
        $this->loadData();
        $this->manager->flush();
    }
}