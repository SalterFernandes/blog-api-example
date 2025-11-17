<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Data\Comment\CreateCommentData;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    public function index(int $postId, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $comments = $this->commentService->getByPost($postId, $perPage);

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    public function store(CreateCommentData $data, Request $request): JsonResponse
    {
        $comment = $this->commentService->create($data, $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Comentário criado com sucesso',
            'data' => $comment
        ], 201);
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $deleted = $this->commentService->delete($id, $request->user()->id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Comentário não encontrado ou não autorizado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comentário eliminado com sucesso'
        ], 204);
    }
}
