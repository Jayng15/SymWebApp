<?php

namespace App\Controller;

use App\Entity\MicroPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LikeController extends AbstractController
{
    #[Route('/like/{post}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(
        MicroPost $post,
        EntityManagerInterface $manager,
        Request $request
    ): Response{
        $currentUser = $this->getUser();
        $post->addLikedBy($currentUser);    
        $manager->persist($post);
        $manager->flush();

        return $this->redirect($request->headers->get('referer')); 
    }
    
    #[Route('/unlike/{post}', name: 'app_unlike')]
    public function unLike(
        MicroPost $post,
        EntityManagerInterface $manager,
        Request $request
    ): Response{
        $currentUser = $this->getUser();
        $post->removeLikedBy($currentUser);    
        $manager->persist($post);
        $manager->flush();

        return $this->redirect($request->headers->get('referer')); 
    }

}
