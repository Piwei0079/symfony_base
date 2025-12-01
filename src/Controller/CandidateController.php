<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateStep1Type;
use App\Form\CandidateStep2Type;
use App\Form\CandidateStep3Type;
use App\Form\CandidateStep4Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CandidateController extends AbstractController
{
    #[Route('/candidate/apply/{step}', name: 'app_candidate_apply', requirements: ['step' => '\d+'], defaults: ['step' => 1])]
    public function apply(int $step, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        
        // 1. Retrieve or Initialize Candidate
        // We check if we have data in session. If we are at step 1 and no data, we start fresh.
        // Otherwise we try to load from session.
        if ($step === 1 && !$session->has('candidate_data')) {
            $candidate = new Candidate();
        } else {
            $candidate = $session->get('candidate_data');
            
            // Safety check: if session expired or direct access to step > 1 without data
            if (!$candidate instanceof Candidate) {
                return $this->redirectToRoute('app_candidate_apply', ['step' => 1]);
            }
        }

        // 2. Determine Form Type
        $formClass = match ($step) {
            1 => CandidateStep1Type::class,
            2 => CandidateStep2Type::class,
            3 => CandidateStep3Type::class,
            4 => CandidateStep4Type::class,
            default => throw $this->createNotFoundException('Étape invalide'),
        };

        // 3. Conditional Logic: Skip Step 3 if hasExperience is false
        if ($step === 3 && !$candidate->isHasExperience()) {
            return $this->redirectToRoute('app_candidate_apply', ['step' => 4]);
        }

        // 4. Create and Handle Form
        $form = $this->createForm($formClass, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 5. Save to Session
            $session->set('candidate_data', $candidate);

            // 6. Determine Next Step
            $nextStep = $step + 1;

            // Conditional jump
            if ($step === 2 && !$candidate->isHasExperience()) {
                $nextStep = 4;
            }

            // 7. Final Step Persistence
            if ($step === 4) {
                $candidate->setStatus('submitted');
                $entityManager->persist($candidate);
                $entityManager->flush();

                // Clear session
                $session->remove('candidate_data');

                return $this->redirectToRoute('app_candidate_success');
            }

            return $this->redirectToRoute('app_candidate_apply', ['step' => $nextStep]);
        }

        return $this->render('candidate/apply.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'candidate' => $candidate,
        ]);
    }

    #[Route('/candidate/success', name: 'app_candidate_success')]
    public function success(): Response
    {
        return $this->render('candidate/success.html.twig');
    }
}
