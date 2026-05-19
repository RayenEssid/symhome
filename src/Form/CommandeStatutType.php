<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'label'   => 'Statut de la commande',
                'choices' => [
                    'En attente' => Commande::STATUT_EN_ATTENTE,
                    'En cours'   => Commande::STATUT_EN_COURS,
                    'Complétée'  => Commande::STATUT_COMPLETEE,
                    'Annulée'    => Commande::STATUT_ANNULEE,
                ],
            ])
            ->add('sauvegarder', SubmitType::class, [
                'label' => 'Mettre à jour',
                'attr'  => ['class' => 'btn btn-primary mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Commande::class]);
    }
}
