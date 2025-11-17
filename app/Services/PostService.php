<?php

namespace App\Services;

use App\Data\Post\PostData;
use App\Data\Post\CreatePostData;
use App\Data\Post\UpdatePostData;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

readonly class PostService
{
    public function __construct(
        private PostRepository $postRepository
    ) {}

    public function getAll(int $perPage = 15): PaginatedDataCollection
    {
        $posts = $this->postRepository->paginate($perPage, ['author']);

        return PostData::collect($posts, PaginatedDataCollection::class);
    }

    public function getById(int $id): ?PostData
    {
        $post = $this->postRepository->findById($id, ['author', 'comments.author', 'categories']);

        return $post ? PostData::from($post)->include('author', 'comments', 'categories', 'commentsCount') : null;
    }

    public function getBySlug(string $slug): ?PostData
    {
        $post = $this->postRepository->findBySlug($slug, ['author', 'comments.author', 'categories']);

        return $post ? PostData::from($post)->include('author', 'comments', 'categories', 'commentsCount') : null;
    }

    public function getByUser(int $userId, int $perPage = 15): PaginatedDataCollection
    {
        $posts = $this->postRepository->getByUser($userId, $perPage, ['author']);

        return PostData::collect($posts, PaginatedDataCollection::class);
    }

    public function getByCategory(int $categoryId, int $perPage = 15): PaginatedDataCollection
    {
        $posts = $this->postRepository->getByCategory($categoryId, $perPage, ['author']);

        return PostData::collect($posts, PaginatedDataCollection::class);
    }

    public function create(CreatePostData $data, int $userId): PostData
    {
        $post = $this->postRepository->create([
            'title' => $data->title,
            'content' => $data->content,
            'excerpt' => $data->excerpt,
            'user_id' => $userId,
            'published_at' => now(),
        ]);

        if (!empty($data->categoryIds)) {
            $this->postRepository->syncCategories($post, $data->categoryIds);
        }

        return PostData::from($post->load('author', 'categories'));
    }

    public function update(int $id, UpdatePostData $data): ?PostData
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return null;
        }

        $updateData = [];

        if (!$data->title instanceof \Spatie\LaravelData\Optional) {
            $updateData['title'] = $data->title;
        }

        if (!$data->content instanceof \Spatie\LaravelData\Optional) {
            $updateData['content'] = $data->content;
        }

        if (!$data->excerpt instanceof \Spatie\LaravelData\Optional) {
            $updateData['excerpt'] = $data->excerpt;
        }

        if (!empty($updateData)) {
            $this->postRepository->update($post, $updateData);
        }

        if (!$data->categoryIds instanceof \Spatie\LaravelData\Optional) {
            $this->postRepository->syncCategories($post, $data->categoryIds);
        }

        return PostData::from($post->fresh(['author', 'categories']));
    }

    public function delete(int $id): bool
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return false;
        }

        return $this->postRepository->delete($post);
    }
}
