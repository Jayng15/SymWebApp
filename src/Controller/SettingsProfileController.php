<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(
        Request $request,
        // UserProfileRepository $profiles,
        EntityManagerInterface $manager,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UserProfile $userProfile */
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);

            $manager->persist($user); 
            $manager->flush();
            
            $this->addFlash('success', 'Your personal profile has been updated');
            
            return $this->redirectToRoute('app_settings_profile');
        }
        return $this->render(
            'settings_profile/profile.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
