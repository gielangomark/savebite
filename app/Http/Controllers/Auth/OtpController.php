<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class OtpController extends Controller {
    public function create(Request $request) {
        if (! $request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }
    public function store(Request $request) {
        $request->validate(['code' => ['required','digits:6']]);
        $otp = LoginOtp::where('user_id', $request->session()->get('otp_user_id'))->whereNull('used_at')->latest()->first();
        if (!$otp || $otp->expires_at->isPast() || $otp->attempts >= 5 || !Hash::check($request->code, $otp->code)) {
            if ($otp) $otp->increment('attempts'); return back()->withErrors(['code' => 'Kode OTP salah, kedaluwarsa, atau sudah terlalu banyak percobaan.']);
        }
        $otp->update(['used_at' => now()]);
        $user = User::findOrFail($otp->user_id);
        $remember = $request->session()->pull('otp_remember', false);
        Auth::guard('web')->login($user, $remember);
        $request->session()->regenerate();
        $request->session()->save();
        $redirect = $request->session()->pull('otp_redirect', route('home'));
        $request->session()->forget('otp_user_id');
        return $user->isMerchant() ? redirect()->route('merchant.dashboard') : redirect()->to($redirect);
    }
    public function resend(Request $request) { $user = User::findOrFail($request->session()->get('otp_user_id')); $this->issue($user, $request, 'login'); return back()->with('status', 'Kode OTP baru sudah dikirim.'); }
    public function issue(User $user, Request $request, string $purpose = 'login'): void {
        $code = (string) random_int(100000, 999999);
        LoginOtp::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);
        LoginOtp::create(['user_id' => $user->id, 'code' => Hash::make($code), 'purpose' => $purpose, 'expires_at' => now()->addMinutes(10)]);
        $request->session()->put('otp_user_id', $user->id);
        Mail::raw("Kode OTP Save Bite: {$code}. Berlaku 10 menit. Jangan berikan kode ini kepada siapa pun.", function ($mail) use ($user) { $mail->to($user->email)->subject('Kode OTP Save Bite'); });
    }
}
