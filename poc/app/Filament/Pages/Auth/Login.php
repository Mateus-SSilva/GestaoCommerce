<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/** Login da POC: já vem preenchido com o usuário de demonstração. */
class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'gestor@demo.test',
            'password' => 'demo1234',
            'remember' => true,
        ]);
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString('Versão experimental (POC) · usuário de demonstração já preenchido');
    }
}
