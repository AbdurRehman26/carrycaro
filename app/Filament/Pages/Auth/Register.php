<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function loginAction(): Action
    {
        return parent::loginAction()->label('Sign in');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getFacebookProfileFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone_number')->label('Phone Number (Optional)')
            ->tel()
            ->maxLength(15)
            ->placeholder('+4695XXXXXXXX')
            ->required(false);
    }

    protected function getFacebookProfileFormComponent(): Component
    {
        return TextInput::make('facebook_profile')->label('Facebook Profile Url (Optional)');
    }

    protected function handleRegistration(array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var User $user */
        $user = parent::handleRegistration($data);

        $user->sendEmailVerificationNotification();

        return $user;
    }
}
