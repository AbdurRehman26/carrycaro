<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

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
        return PhoneInput::make('phone_number')
            ->countryStatePath('phone_country')
            ->displayNumberFormat(PhoneInputNumberType::NATIONAL)
            ->strictMode()
            ->label('Phone Number (Optional)');
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
