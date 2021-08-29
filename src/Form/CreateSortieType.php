<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    private $em;

    /**
     * @param EntityManagerInterface $em
     */

//    public function __construct(EntityManagerInterface $em)
//    {
//        $this->em = $em;
//    }

    /**
     *{@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie:',
                'required' => true
            ])
            ->add('dateHeureDebut', DateType::class, [
                'label' => 'Date et heure de la sortie:',
                'html5' => true,
                'widget' => 'single_text',
                'required' => true,
                'empty_data'=>null
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d inscription:',
                'html5' => true,
                'widget' => 'single_text',
                'required' => true,
                'empty_data'=>null
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de places:',
                'required' => true
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée(minutes):',
                'required' => false,
                'attr' => [
                    'min' => 15,
                    'max' => 90,
                    'step' => 5
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos:',
                'required' => false

            ])
            ->add('campus', EntityType::class, ['class' => Campus::class, 'choice_label' => 'nom',
                'label' => 'Campus:'])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'label' => 'Ville:',
                'required' => false
            ])
            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'label' => 'Lieu:',
                'required' => false
            ])
        ->add('rue', TextType::class,[
        'mapped'=>false,
        'label'=>'rue:',
            'required'=>false])
            ->add('cp', IntegerType::class,[
                'mapped'=>false,
                'label'=>'Code postale:',
                'required'=>false
                ])
            ->add('latitude',IntegerType::class,[
                'mapped'=>false,
                'label'=>'Latitude:',
                'required'=>false])
                ->add('longitude',IntegerType::class,[
                    'mapped'=>false,
                    'label'=>'Longitude:',
                    'required'=>false]);

        $formModifier = function (FormInterface $form, Ville $ville = null) {
            $lieux = null === $ville ? [] : $ville->getLieux();
            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choices' => $lieux,
                'required' => false,
                'choice_label' => 'nom',
                'disabled'=>true,
                'attr' => ['class' => 'custom-select']
            ]);
        };
        $builder->get('ville')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $ville = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $ville);

            }
        );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }


}
