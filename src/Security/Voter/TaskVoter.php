<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin can do anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
            case self::VIEW:
                return $this->canView($task, $user);
            case self::DELETE:
                return $this->canDelete($task, $user);
        }

        return false;
    }

    private function canEdit(Task $task, UserInterface $user): bool
    {
        // Author can edit
        return $user === $task->getAuthor();
    }

    private function canView(Task $task, UserInterface $user): bool
    {
        // Author can view
        return $user === $task->getAuthor();
    }

    private function canDelete(Task $task, UserInterface $user): bool
    {
        // Only admin can delete (handled in voteOnAttribute), so return false here for regular users
        return false;
    }
}
