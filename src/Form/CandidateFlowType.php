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
            ->addStep('userInfo', CandidateStep1Type::class, ['inherit_data' => true])
            ->addStep('experienceDetails', CandidateStep2Type::class, ['inherit_data' => true], fn(Candidate $data) => !$data->isHasExperience())
            ->addStep('availability', CandidateStep3Type::class, ['inherit_data' => true])
            ->addStep('consentRGPD', CandidateStep4Type::class, ['inherit_data' => true]);
        $builder->addStep('review', ReviewType::class, ['inherit_data' => true]);
        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
            'step_property_path' => 'currentStep',
        ]);
    }
}
