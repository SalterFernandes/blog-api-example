<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentRepository
{
    public function findById(int $id): ?Comment
    {
        return Comment::with('author')->find($id);
    }

    public function getByPost(int $postId, int $perPage = 15): LengthAwarePaginator
    {
        return Comment::with('author')
            ->where('post_id', $postId)
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function update(Comment $comment, array $data): bool
    {
        return $comment->update($data);
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }
}
