<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/', name: 'list')]
    public function list(): JsonResponse
    {
        return new JsonResponse(self::POSTS);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show($id): JsonResponse
    {
        return new JsonResponse(self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]);
    }

    #[Route('/{slug}', name: 'show_slug')]
    public function showSlug($slug): JsonResponse
    {
        return new JsonResponse(self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]);
    }
}
