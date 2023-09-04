<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsFormType extends AbstractType
{
    // CE FORMULAIRE SERT POUR L'AJOUT ET LA MODIFICATION DES PRODUITS
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom'
            ])
            ->add('description', options: [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Description'
            ])
            ->add('price', options:[
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prix'
            ])
            ->add('stock', options:[
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Unités en stock'
            ])
            ->add('categories', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'group_by' => 'parent.name',
                'query_builder' => function(CategoriesRepository $categoriesRepository)
                {
                    return $categoriesRepository->createQueryBuilder('categorie')
                    ->where('categorie.parent IS NOT NULL')
                    ->orderBy('categorie.name', 'ASC');
                }
            ])
            //On ajoute l'upload d'image multiple dans le formulaire
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
