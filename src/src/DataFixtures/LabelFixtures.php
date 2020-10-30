<?php


namespace App\DataFixtures;


use App\Entity\Label;

class LabelFixtures extends AbstractFixtures
{
    protected function loadData(): void
    {
       $this->createMany(15,'label', function(){
           return (new Label())
               ->setName($this->faker->lastName . ' Records')
               ->setPromote(false)
               ->setDescription($this->faker->optional()->realText())
           ;
       });
    }
}