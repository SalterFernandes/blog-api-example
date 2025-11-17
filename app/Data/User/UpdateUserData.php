<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Optional;

class UpdateUserData extends Data
{
    public function __construct(
        #[Min(3)]
        public string|Optional $name,

        #[Email]
        public string|Optional $email,

        #[Min(8)]
        public string|Optional $password,
    ) {}

    public static function messages(): array
    {
        return [
            'name.min' => 'O nome deve ter no mínimo 3 caracteres',
            'email.email' => 'O email deve ser válido',
            'password.min' => 'A password deve ter no mínimo 8 caracteres',
        ];
    }
}
