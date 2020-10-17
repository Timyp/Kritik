<?php


namespace App\DataFixtures;


use App\Entity\Note;
use App\Entity\Record;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NoteFixtures extends AbstractFixtures implements DependentFixtureInterface
{

    protected function loadData(): void
    {
        $this->createMany(350, 'note', function () {
            /** @var User $author */
            $author = $this->getRandomReference('user');
            /** @var Record $record */
            $record = $this->getRandomReference('record');

            return (new Note())
                ->setAuthor($author)
                ->setRecord($record)
                ->setValue($this->faker->numberBetween(0,10))
                ->setComment($this->faker->optional()->realText())
                ;
        });
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            RecordFixtures::class,
        ];
    }
}