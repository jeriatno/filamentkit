<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LogoutConfirmation extends Component
{
    public function logout()
    {
        $this->dispatch('close-modal', ['id' => 'logout-modal']);

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('filament.admin.auth.login');
    }

    public function render()
    {
        return view('livewire.logout-confirmation');
    }
}
