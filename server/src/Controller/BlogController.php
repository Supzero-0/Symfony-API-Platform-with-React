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
    private const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello world!',
        ],
        [
            'id' => 2,
            'slug' => 'another-post',
            'title' => 'This is another post!',
        ],
        [
            'id' => 3,
            'slug' => 'last-example',
            'title' => 'This is the last example!',
        ],
    ];

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
    public function show($id, BlogPostRepository $blogPostRepository): JsonResponse
    {
        return $this->json($blogPostRepository->find($id));
    }

    #[Route('/show/{slug}', name: 'show_slug')]
    public function showSlug($slug, BlogPostRepository $blogPostRepository): JsonResponse
    {
        return $this->json($blogPostRepository->findBy(['slug' => $slug]));
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, BlogPostRepository $blogPostRepository)
    {
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $blogPostRepository->save($blogPost, true);

        return $this->json($blogPost);
    }
}
