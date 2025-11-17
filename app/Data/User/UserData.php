<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use App\Data\Post\PostData;
use Carbon\Carbon;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        #[MapName('email_verified_at')]
        public ?Carbon $emailVerifiedAt,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        #[MapName('created_at')]
        public Carbon $createdAt,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        #[MapName('updated_at')]
        public Carbon $updatedAt,

        /** @var Lazy|DataCollection<PostData> */
        public Lazy|DataCollection $posts,
    ) {}
}
