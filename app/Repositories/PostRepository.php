<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PostRepository
{
    public function findById(int $id, array $with = []): ?Post
    {
        return Post::with($with)->find($id);
    }

    public function findBySlug(string $slug, array $with = []): ?Post
    {
        return Post::with($with)->where('slug', $slug)->first();
    }

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return Post::with($with)
            ->withCount('comments')
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::where('user_id', $userId)
            ->withCount('comments')
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })
            ->withCount('comments')
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function syncCategories(Post $post, array $categoryIds): void
    {
        $post->categories()->sync($categoryIds);
    }
}
