<?php

namespace App\Data\Post;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use App\Data\User\UserData;
use App\Data\Comment\CommentData;
use App\Data\Category\CategoryData;
use Carbon\Carbon;

class PostData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $content,
        public string $excerpt,

        #[MapName('published_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public ?Carbon $publishedAt,

        #[MapName('created_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $createdAt,

        #[MapName('updated_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $updatedAt,

        public Lazy|UserData $author,

        /** @var Lazy|DataCollection<CommentData> */
        public Lazy|DataCollection|null $comments = null,

        /** @var Lazy|DataCollection<CategoryData> */
        public Lazy|DataCollection|null $categories = null,

        #[Computed]
        public int $commentsCount = 0,
    ) {}
}
