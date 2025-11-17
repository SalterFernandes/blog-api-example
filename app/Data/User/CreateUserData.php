<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Unique;

class CreateUserData extends Data
{
    public function __construct(
        #[Required, Min(3)]
        public string $name,

        #[Required, Email, Unique('users', 'email')]
        public string $email,

        #[Required, Min(8), Confirmed]
        public string $password,
    ) {}

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser válido',
            'email.unique' => 'Este email já está registado',
            'password.required' => 'A password é obrigatória',
            'password.min' => 'A password deve ter no mínimo 8 caracteres',
            'password.confirmed' => 'As passwords não coincidem',
        ];
    }
}
