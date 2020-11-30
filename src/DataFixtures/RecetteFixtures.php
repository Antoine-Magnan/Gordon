<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecetteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=0;$i<10;$i++){
            $product = new Recette();
            $product->setTitle("titre de l'article n°$i")
                    ->setContent("<p>contenu de l'article n°$i</p>")
                    ->setIngredients(4)
                    ->setCreatedAt(new \DateTime());
            $manager->persist($product);
        }

        $manager->flush();

    }
}
