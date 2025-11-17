<?php

namespace App\Data\Category;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use App\Data\Post\PostData;
use Carbon\Carbon;

class CategoryData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,

        #[MapName('created_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $createdAt,

        #[MapName('updated_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $updatedAt,

        /** @var Lazy|DataCollection<PostData> */
        public Lazy|DataCollection|null $posts = null,

        #[Computed]
        public int $postsCount = 0,
    ) {}
}
