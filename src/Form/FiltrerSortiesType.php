<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use phpDocumentor\Reflection\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrerSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //On fera le filtre sur les champs :
        // Campus / nom de la sortie / entre date et date / sortie que j'organise / sortie auxquelles je suis inscrit
        // sortie auxquelles je ne suis pas inscrit / sorties passees

        $builder

            // Ici on veut une liste déroulante avec tous les différents campus
            ->add('campus',EntityType::class,[
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false,
            ])

            // Ici on veut une barre de recherche sur un mot clé
            ->add('nom',SearchType::class, [
                'label' => 'Le nom de la sortie contient',
                'required' => false,
            ])

        //Ici on veut une selection de date dans un petit calendrier donc en type de donnee est datetype que l'on importe
        //todo gérer le datepicker
            ->add('dateHeureDebut',DateType::class, [
                'label' => 'Entre le',
                'html5' => true,
                'widget' => 'single_text',

            ])

        //Idem pour la date de fin
        //todo gérer le datepicker
            ->add('dateLimiteInscription', DateType::class, [
            'label' => 'et le',
            'html5' => true,
            'widget' => 'single_text',

        ])

            //Champ inutile pour ce form
            // ->add('nbInscriptionMax')


            //Ici je modifie, pas sur que ce soit bon
            ->add('Organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur",
                'required' => false,
            ])



            //->add('lieu')
            //->add('etat')
            //->add('participants')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
