<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\UserProfile;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{

    #[Route('/', name: 'app_index')]
    public function index(
        MicroPostRepository $posts,
        EntityManagerInterface $entityManager,
        CommentRepository $comments
    ): Response
    {
        return $this->render('hello/index.html.twig',);
    }

}
