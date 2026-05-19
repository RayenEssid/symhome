<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresseLivraison', TextType::class, [
                'label'       => 'Adresse de livraison',
                'attr'        => ['placeholder' => 'Ex : 12 Rue de la Paix, Tunis 1000'],
                'constraints' => [
                    new NotBlank(message: 'L\'adresse de livraison est obligatoire.'),
                    new Length(min: 10, minMessage: 'Veuillez entrer une adresse complète (min {{ limit }} caractères).'),
                ],
            ])
            ->add('valider', SubmitType::class, [
                'label' => 'Confirmer la commande',
                'attr'  => ['class' => 'btn btn-success btn-lg w-100 mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Commande::class]);
    }
}
