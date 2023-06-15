<?php

namespace App\DataFixtures;

use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class ProductsFixtures extends Fixture
{

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($prod = 1; $prod <= 10; $prod++) {

            $product = new Products();
            $product->setName($faker->text(10)); // 200 charactère max, j'établis ici à 10 minimum
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(400, 55000)); // Entre 40 et 550 euro
            $product->setStock($faker->numberBetween(0, 20)); // Le stock entre 0 et 20

            // On va chercher une référence de catégorie
            $category = $this->getReference('cat-' . rand(2, 4)); // 2 et 4 sont les 3 catégories de chaussure, cela va créer des données aléatoirement dans ces 3 catégories
            $product->setCategories($category);

            $this->setReference('prod-'.$prod, $product);
            $manager->persist($product);
            
        }

        $manager->flush();
    }


}
