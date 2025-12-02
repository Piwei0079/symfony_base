<?php

namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('availabilityDate', DateType::class, [
                'label' => 'Date de disponibilité',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'],
                'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
            ])
            ->add('isAvailableImmediately', CheckboxType::class, [
                'label' => 'Disponible immédiatement',
                'required' => false,
                'label_attr' => ['class' => 'ml-2 text-gray-700 text-sm font-bold'],
                'attr' => ['class' => 'form-checkbox h-5 w-5 text-blue-600'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Candidate::class,
        ]);
    }
}
