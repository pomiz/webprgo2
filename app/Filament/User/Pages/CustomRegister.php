<?php

namespace App\Filament\User\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomRegister extends Register
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->unique(User::class),

                TextInput::make('password')
                    ->label('Password')
                    ->required()
                    ->password()
                    ->minLength(8)
                    ->confirmed(),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->required()
                    ->password()
                    ->minLength(8),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', // Force role to user
        ]);

        return $user;
    }
}