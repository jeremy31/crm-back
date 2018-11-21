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
            ->add('reference')
            ->add('priceTe')
            ->add('priceTi')
            ->add('tax')
            ->add('weightQuantity')
            ->add('unity')
            ->add('active')
            ->add('dateAdd')
            ->add('dateUpdate')
            ->add('product')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductDeclension::class,
        ]);
    }
}
