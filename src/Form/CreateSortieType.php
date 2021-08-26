<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
                'label'=>'Nom de la sortie:'
            ])
            ->add('dateHeureDebut',DateType::class,[
                'label'=>'Date et heure de la sortie:',
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('dateLimiteInscription',DateType::class,[
                'label'=>'Date limite d inscription:',
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('nbInscriptionMax',TextType::class,[
                'label'=>'Nombre de places:'
            ])
            ->add('duree',IntegerType::class,[
                'label'=>'Durée(minutes):',
                'attr'=>[
                    'min'=>15,
                    'max'=>90,
                    'step'=>5
                ]
            ])
            ->add('infosSortie', TextareaType::class,[
                'label'=>'Description et infos:',

            ])
            ->add('campus', EntityType::class,['class'=>Campus::class, 'choice_label'=>'nom',
                'label'=>'Campus:'])
            ->add('ville', EntityType::class,['class'=>Ville::class,'choice_label'=>'nom',
                'mapped'=>false,
                'label'=>'Ville:'])
            ->add('lieu', EntityType::class,['class'=>Lieu::class,'choice_label'=>'nom',
                'label'=>'Lieu::'])
            ->add('rue', EntityType::class,['class'=>Lieu::class,
                'mapped'=>false,
                'label'=>'rue:'])
            ->add('cp', EntityType::class,['class'=>Ville::class,
                'mapped'=>false,
                'label'=>'Code postale:'
                ])
            ->add('latitude',EntityType::class,['class'=>Lieu::class,
               'mapped'=>false])
            ->add('longtitude',EntityType::class,['class'=>Lieu::class,
                'mapped'=>false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
