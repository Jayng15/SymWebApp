<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\MicroPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class MicroPostVoter extends Voter
{
    public function __construct(
        private Security $security
    )
    {
        
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [MicroPost::EDIT, MicroPost::VIEW]);
    }

    /**
     * @param MicroPost $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $isAuth = $user instanceof UserInterface;

        if($this->security->isGranted('ROLE_ADMIN'))
        {
            return true;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case MicroPost::EDIT:
                return $isAuth 
                && ($subject->getAuthor()->getId() == $user->getId()) ||
                $this->security->isGranted('ROLE_EDITOR');
            case MicroPost::VIEW:
                return true;
                // logic to determine if the user can VIEW
                // return true or false
        }

        return false;
    }
}
