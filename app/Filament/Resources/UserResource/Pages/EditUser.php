<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    if ($this->record->id === auth()->id()) {
                        $action->cancel();
                        $action->failureNotificationTitle('You cannot delete your own account');
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash password if provided
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password fields if empty (keep existing password)
            unset($data['password'], $data['password_confirmation']);
        }

        return $data;
    }
}