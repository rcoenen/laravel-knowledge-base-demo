<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

/**
 * The admin login, filled in with the demo staff account.
 */
class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => config('demo.email'),
            'password' => config('demo.password'),
            'remember' => true,
        ]);
    }
}
