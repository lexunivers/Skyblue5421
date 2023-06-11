<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//use Symfony\Component\Form\Extension\Core\Type\EntityType;
use App\Form\ReservationType;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vol;
use Doctrine\ORM\EntityRepository;

class VolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reservataire = $options['reservataire'];

        $builder
        ->add('datevol', DateType::class, [
            'attr' => array(
            'placeholder' => 'Cliquez  pour le Calendrier',
            ),
            'years' => range(2020, 2023),
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'dd/MM/yyyy',              
            ])
            
        ->add('user', null, array('required' => false,'disabled' => true,))
        ->add('avion', null, array('placeholder' => 'Quel Appareil ?'))
        ->add('typevol', null, array('placeholder' => 'Choisissez !'))
        ->add('instructeur', null, array('placeholder' => 'Si Vol Ecole'))
        ->add('naturevol', null, array('placeholder' => 'Pour les Stats !'))
        ->remove('reservataire', EntityType::Class, ['class' => Reservation::class,'choice_label' => 'reservataire'])//, null, array('attr'=> array('placeholder' => 'Réservataire !') ))

        ->add('CodeReservation', EntityType::class, [
                'class' => Reservation::class,
                'query_builder' => function (EntityRepository $er) use ($reservataire){
                    return $er->createQueryBuilder('r')
                    ->where('r.reservataire =:reservataire')
                    ->setParameter('reservataire',$reservataire)
                    ;
                },
            'choice_label' => 'CodeReservation',
        ])        

       // ->add('CodeReservation', null,  array('attr'=> array('placeholder' => 'N° de Réservation !') ))
        //->add('CodeReservation',EntityType::class,['class' => Reservation::class,'choice_label' => 'CodeReservation'])        

                    

        ->add('lieuDepart', null, array('placeholder' => 'Décollage de'))
        ->add('heureDepart')																
        ->add('lieuArrivee', null, array('placeholder' => 'Atterissage à'))
        ->add('heureArrivee')
        ->add('valider', SubmitType::class, array('label' => 'Valider'))                
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Vol',
            //'user' => null,
            'reservataire' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_entity_vol';
    }
}
