<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class EditProfile extends \Filament\Pages\Auth\EditProfile
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent()->disabled(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getFacebookProfileFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
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
}
