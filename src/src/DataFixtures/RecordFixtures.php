<?php


namespace App\DataFixtures;


use App\Entity\Artist;
use App\Entity\Record;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RecordFixtures extends AbstractFixtures implements DependentFixtureInterface
{

    protected function loadData(): void
    {
       $this->createMany(100, 'record', function () {
           //On récupère un objet artiste grave au système de références
           /**
            * @var Artist $artist
            */
           $artist = $this->getRandomReference('artist');

           /**
            * @var Label/null $label
            */
           $label = $this->faker->boolean(75) ? $this->getRandomReference('label') : null;

           return (new Record())
               ->setTitle($this->faker->catchPhrase)
               ->setDescription($this->faker->realText())
               ->setReleasedAt($this->faker->dateTimeBetween('-6 months'))
               ->setArtist($artist)
               ->setLabel($label)
               ;
       });
    }

    public function getDependencies()
    {
       return [
           ArtistFixtures::class,
           LabelFixtures::class
       ];
    }
}