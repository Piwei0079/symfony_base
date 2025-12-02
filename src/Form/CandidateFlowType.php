<?php

namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\Flow\AbstractFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\Form\Flow\Type\NavigatorFlowType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder
            ->addStep('personal', CandidateStep1Type::class, [
                'inherit_data' => true,
                'label' => 'Informations personnelles',
            ])
            ->addStep('experience', CandidateStep2Type::class, [
                'inherit_data' => true,
                'label' => 'Expérience',
            ])
            ->addStep('availability', CandidateStep3Type::class, [
                'inherit_data' => true,
                'label' => 'Disponibilité',
            ])
            ->addStep('consent', CandidateStep4Type::class, [
                'inherit_data' => true,
                'label' => 'Consentement',
            ])
            ->addStep('summary', CandidateSummaryType::class, [
                'inherit_data' => true,
                'label' => 'Résumé',
            ])
            ->add('navigator', NavigatorFlowType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
            'step_property_path' => 'currentStep',
        ]);
    }
}
