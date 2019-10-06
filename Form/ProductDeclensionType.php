<?php

namespace App\Form;

use App\Entity\ProductDeclension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDeclensionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product')
            ->add('reference')
            ->add('priceTe')
            ->add('priceTi')
            ->add('tax')
            ->add('unity')
            ->add('weightQuantity')
            ->add('active')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductDeclension::class,
        ]);
    }
}
