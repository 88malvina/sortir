<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    //lieux
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('nom', TextType::class,[
                'label'=>'Nom',
                'required'=>true
            ])

            ->add('rue', TextType::class,[
                'label'=>'Rue',
                'required'=>true
            ])
            ->add('latitude',IntegerType::class,[
                'required'=>false
            ])
            ->add('longitude',IntegerType::class,[
                'required'=>false])
            ->add('ville',EntityType::class,[
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'label'=>'Ville'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
