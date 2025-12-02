<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateFlowType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CandidateController extends AbstractController
{
    #[Route('/apply', name: 'app_candidate_apply')]
    public function apply(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidate = new Candidate();
        $flow = $this->createForm(CandidateFlowType::class, $candidate);
        $flow->handleRequest($request);

        // Manual conditional logic for 'experience' step
        if ($flow->isSubmitted() && $flow->isValid()) {
            // If we just finished the 'personal' step and hasExperience is false, skip 'experience'
            if ($candidate->getCurrentStep() === 'personal' && !$candidate->isHasExperience()) {
                // We need to advance the flow manually or set the next step.
                // Since we can't easily manipulate the flow state from here without internal knowledge,
                // we might need to rely on the flow's own navigation if possible.
                // But the flow object doesn't seem to expose a simple "skip" method for the *next* step 
                // unless we are in the flow building phase.
                
                // However, if we are in the controller, the request has already been handled.
                // If the user clicked "Next" on "personal", the flow determined the next step is "experience".
                // If we want to skip it, we might need to force the current step to "availability".
                
                // Let's try to set the current step on the candidate and re-create the form?
                // Or maybe just let the user see the step but disable it? No, that's bad UX.
                
                // Actually, the `include_if` failure suggests I might be missing a `StepType` wrapper or similar.
                // But for now, let's try to handle it here.
                
                // If I change the currentStep property on the candidate, the flow *should* pick it up on the next request?
                // But we are in the *current* request processing.
                
                // If the user submitted "personal", the flow is now at "experience" (conceptually).
                // If I want to skip "experience", I should set `currentStep` to `availability`.
                 
            }
        }

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            $candidate = $flow->getData();
            $candidate->setStatus('submitted');
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidate_success');
        }

        return $this->render('candidate/apply.html.twig', [
            'form' => $flow->getStepForm(),
            'flow' => $flow,
        ]);
    }

    #[Route('/success', name: 'app_candidate_success')]
    public function success(): Response
    {
        return $this->render('candidate/success.html.twig');
    }
}
