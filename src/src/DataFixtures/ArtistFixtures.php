<?php


namespace App\DataFixtures;


use App\Entity\Artist;

class ArtistFixtures extends AbstractFixtures
{
    protected function loadData(): void
    {
        $this->createMany(50, 'artist', function () {
            return (new Artist())
                ->setName($this->faker->name)
                ->setDescription($this->faker->optional()->realText())
                ;
        });
    }
}