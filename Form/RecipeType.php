<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Nom'))
            ->add('title', null, array('label' => 'Titre'))
            ->add('price', null, array('label' => 'Prix'))
            ->add('price20', null, array('label' => 'Prix 20g'))
            ->add('price150', null, array('label' => 'Prix 150g'))
            ->add('stock', null, array('label' => 'Stock'))
            ->add('stock20', null, array('label' => 'Stock 20g'))
            ->add('stock45', null, array('label' => 'Stock 45g'))
            ->add('stock150', null, array('label' => 'Stock 150g'))
            ->add('weight', null, array('label' => 'Poids'))
            ->add('prestashop', null, array('label' => 'ID Prestashop'))
            ->add('brewTime', null, array('label' => 'Temps d\'infusion'))
            ->add('brewTemp', null, array('label' => 'Degré d\'infusion'))
            ->add('description', null, array('label' => 'Description', 'attr' => array('rows' => '10','cols' => '50')))
            ->add('active', null, array('label' => 'En ligne'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
