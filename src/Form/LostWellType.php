<?php

namespace App\Form;

use App\Entity\LostWell;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LostWellType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exist', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Existant' => 'existant',
                    'Non existant' => 'non existant'
                ]
            ])
            ->add('functional', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Fonctionnel' => 'fonctionnel',
                    'Non fonctionnel' => 'non fonctionnel'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LostWell::class,
        ]);
    }
}
