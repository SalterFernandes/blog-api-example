<?php

namespace App\Services;

use App\Data\Comment\CommentData;
use App\Data\Comment\CreateCommentData;
use App\Repositories\CommentRepository;
use Spatie\LaravelData\PaginatedDataCollection;

readonly class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    public function getByPost(int $postId, int $perPage = 15): PaginatedDataCollection
    {
        $comments = $this->commentRepository->getByPost($postId, $perPage);

        return CommentData::collect($comments, PaginatedDataCollection::class)
            ->include('author');
    }

    public function create(CreateCommentData $data, int $userId): CommentData
    {
        $comment = $this->commentRepository->create([
            'content' => $data->content,
            'post_id' => $data->postId,
            'user_id' => $userId,
        ]);

        return CommentData::from($comment->load('author'));
    }

    public function delete(int $id, int $userId): bool
    {
        $comment = $this->commentRepository->findById($id);

        if (!$comment || $comment->user_id !== $userId) {
            return false;
        }

        return $this->commentRepository->delete($comment);
    }
}
