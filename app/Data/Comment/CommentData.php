<?php

namespace App\Data\Comment;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use App\Data\User\UserData;
use Carbon\Carbon;

class CommentData extends Data
{
    public function __construct(
        public int $id,
        public string $content,

        #[MapName('post_id')]
        public int $postId,

        #[MapName('created_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $createdAt,

        #[MapName('updated_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $updatedAt,

        public Lazy|UserData $author,
    ) {}
}
