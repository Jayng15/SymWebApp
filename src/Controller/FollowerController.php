<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FollowerController extends AbstractController
{
    #[Route('/follow/{user}', name: 'app_follow')]
    public function follow(
        User $user,
        ManagerRegistry $manager,
        Request $request
    ): Response{
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($user->getId() != $currentUser->getId())
        {
            $currentUser->follow($user);
            $manager->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollow/{user}', name: 'app_unfollow')]
    public function unfollow(
        User $user,
        ManagerRegistry $manager,
        Request $request
    ): Response{
        /** @var User $currentUser */
        $currentUser = $this->getUser();


        if($user->getId() != $currentUser->getId())
        {
            $currentUser->unfollow($user);
            $manager->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
