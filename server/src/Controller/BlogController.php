<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/{page}', name: 'list', defaults: ['page' => 1], requirements: ['page' => '\d+'])]
    public function list($page = 1, Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function ($item) {
                    return $this->generateUrl('blog_show_id', ['id' => $item['id']]);
                }, self::POSTS),
            ]
        );
    }

    #[Route('/show/{id}', name: 'show_id', requirements: ['id' => '\d+'])]
    public function show($id): JsonResponse
    {
        return $this->json(self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]);
    }

    #[Route('/show/{slug}', name: 'show_slug')]
    public function showSlug($slug): JsonResponse
    {
        return $this->json(self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]);
    }
}
