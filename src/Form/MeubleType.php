<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Meuble;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeubleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du meuble',
                'attr'  => ['placeholder' => 'Ex : Canapé 3 places'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => ['rows' => 5, 'placeholder' => 'Description détaillée du meuble...'],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (TND)',
                'scale' => 2,
                'attr'  => ['placeholder' => '0.00', 'min' => 0, 'step' => '0.01'],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock disponible',
                'attr'  => ['placeholder' => '0', 'min' => 0],
            ])
            ->add('categorie', EntityType::class, [
                'class'        => Categorie::class,
                'choice_label' => 'nom',
                'label'        => 'Catégorie',
            ])
            ->add('image', TextType::class, [
                'label'    => 'Chemin de l\'image',
                'required' => false,
                'attr'     => ['placeholder' => 'Ex : /images/canape.jpg'],
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Meuble::class]);
    }
}
