<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{

    private $counter = 1;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Basses', null, $manager);


        $this->createCategory('Baskets', $parent, $manager);
        $this->createCategory('Tongs', $parent, $manager);
        $this->createCategory('Converses', $parent, $manager);

    
        $manager->flush();
    }

    public function createCategory(
        string $name,
        Categories $parent = null,
        ObjectManager $manager

    ) {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        // la Référence est la mise en mémoire d'un élément dans les datafictures
        // Ici ma 1 ere catégorie sera = cat-1, puis cat-2, cat-3, etc...
        $this->addReference('cat-'.$this->counter, $category); 
        $this->counter++;

        return $category;
    }
}

?>