<?php


namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class CandidateStep4Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('consentRGPD', CheckboxType::class, [
                'label' => 'J\'accepte que mes données soient traitées dans le cadre de ma candidature',
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les conditions']),
                ],
                'label_attr' => ['class' => 'ml-2 text-gray-700 text-sm'],
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
