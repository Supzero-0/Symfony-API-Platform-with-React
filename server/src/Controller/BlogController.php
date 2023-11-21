<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route('/{page}', name: 'list', defaults: ['page' => 1], requirements: ['page' => '\d+'])]
    public function list(Request $request, BlogPostRepository $blogPostRepository, $page = 1): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $items = $blogPostRepository->findAll();

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_show_id', ['id' => $item->getId()]);
                }, $items),
            ]
        );
    }

    #[Route('/show/{id}', name: 'show_id', requirements: ['id' => '\d+'])]
    public function show(BlogPost $blogPost): JsonResponse
    {
        // Do find($id) on repository
        return $this->json($blogPost);
    }

    #[Route('/show/{slug}', name: 'show_slug')]
    public function showSlug(BlogPost $blogPost): JsonResponse
    {
        // Do findOneBy(['slug' => contents of {slug}]) on repository
        return $this->json($blogPost);
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, BlogPostRepository $blogPostRepository)
    {
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $blogPostRepository->save($blogPost, true);

        return $this->json($blogPost, 201);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete($id, BlogPostRepository $blogPostRepository)
    {
        $blogPost = $blogPostRepository->find($id);

        $blogPostRepository->remove($blogPost, true);

        return $this->json(null, 204);
    }
}
