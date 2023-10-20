<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $p): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $p->findAllPosts(),
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_trending')]
    public function topLiked(MicroPostRepository $p): Response
    {
        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $p->findAllWithMinLikes(1),
        ]);
    }

    #[Route('/micro-post/followed', name: 'app_micro_post_followed')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function userFollowed(MicroPostRepository $p): Response
    {
        return $this->render('micro_post/followed.html.twig', [
            'posts' => $p->findAllByFollowed($this->getUser())
        ]);
    }


    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(
        MicroPost $post,
        CommentRepository $comments
    ): Response
    {
        return $this->render('/micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }


    #[Route('/micro-post/{id}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'id')]
    public function edit(
        MicroPost $id,
        Request $request,
        MicroPostRepository $p
    ): Response
    {

        $form = $this->createForm(MicroPostType::class, $id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $p->add($post, true);


            // Add a flush message
            $this->addFlash("success", "your post has been updated");
            // Redirect to different page
            return $this->redirectToRoute('app_micro_post');
        }
        return $this->render(
            'micro_post/edit.html.twig',
            [
                'form' => $form,
                'post' => $id
            ]
        );
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: '2')]
    #[IsGranted('ROLE_WRITER')]
    public function add(
        Request $request, 
        MicroPostRepository $p
    ): Response{
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());
            $p->add($post, true);

            // Add a flush message
            $this->addFlash("success", "your post has been upload successfully");

            // Redirect to different page
            return $this->redirectToRoute('app_micro_post');
        }
        return $this->render(
            'micro_post/new.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addComment(
        MicroPost $post,
        Request $request,
        CommentRepository $comments
    ): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $comments->add($comment, true);

            //Add flash
            $this->addFlash('success', 'Your comment has been uploaded');

            return $this->redirectToRoute(
                'app_micro_post_show',
                ['post' => $post->getId()]
            );
        }

        return $this->render(
            'micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }
}
