<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.register', [
            'redirect' => $request->query('redirect', route('home', absolute: false)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->symbols()],
            'role' => ['sometimes', 'in:student,merchant'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->input('role', 'student'),
        ]);

        // Nomor penjual langsung menjadi nomor WhatsApp merchant agar tombol
        // chat pada tiket mengarah ke kontak toko yang benar.
        if ($user->isMerchant()) {
            Merchant::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'location' => 'Kantin Baru',
                'whatsapp' => $user->phone,
            ]);
        }

        event(new Registered($user));

        app(OtpController::class)->issue($user, $request, 'registration');
        $request->session()->put('otp_redirect', $request->input('redirect', route('home')));
        return redirect()->route('otp.create');
    }
}
