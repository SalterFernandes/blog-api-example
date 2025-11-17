<?php

namespace App\Data\Comment;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Exists;

class CreateCommentData extends Data
{
    public function __construct(
        #[Required, Min(5)]
        public string $content,

        #[Required, Exists('posts', 'id')]
        public int $postId,
    ) {}

    public static function messages(): array
    {
        return [
            'content.required' => 'O comentário é obrigatório',
            'content.min' => 'O comentário deve ter no mínimo 5 caracteres',
            'postId.required' => 'O post é obrigatório',
            'postId.exists' => 'Post não encontrado',
        ];
    }
}
