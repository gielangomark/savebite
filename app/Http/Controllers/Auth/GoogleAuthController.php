<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class GoogleAuthController extends Controller {
    public function redirect(Request $request) {
        abort_unless(config('services.google.client_id') && config('services.google.redirect'), 503, 'Google login belum dikonfigurasi.');
        $state = Str::random(40); $request->session()->put(['google_state' => $state, 'google_role' => $request->query('role', 'student'), 'otp_redirect' => $request->query('redirect', route('home'))]);
        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query(['client_id'=>config('services.google.client_id'),'redirect_uri'=>config('services.google.redirect'),'response_type'=>'code','scope'=>'openid email profile','state'=>$state,'access_type'=>'online']));
    }
    public function callback(Request $request, OtpController $otp) {
        abort_unless(hash_equals((string) $request->session()->pull('google_state'), (string) $request->state), 419);
        $token = Http::asForm()->post('https://oauth2.googleapis.com/token',['code'=>$request->code,'client_id'=>config('services.google.client_id'),'client_secret'=>config('services.google.client_secret'),'redirect_uri'=>config('services.google.redirect'),'grant_type'=>'authorization_code'])->throw()->json('access_token');
        $profile = Http::withToken($token)->get('https://openidconnect.googleapis.com/v1/userinfo')->throw()->json();
        abort_unless(($profile['email_verified'] ?? false) && isset($profile['sub'], $profile['email']), 422, 'Email Google harus terverifikasi.');
        $role = $request->session()->pull('google_role', 'student');
        $user = User::firstOrCreate(['email' => $profile['email']], ['name'=>$profile['name'] ?? $profile['email'],'google_id'=>$profile['sub'],'password'=>Str::password(32),'role'=>in_array($role,['student','merchant'])?$role:'student','email_verified_at'=>now()]);
        if (!$user->google_id) $user->update(['google_id'=>$profile['sub']]);
        if ($user->isMerchant()) Merchant::firstOrCreate(['user_id'=>$user->id], ['name'=>$user->name,'location'=>'Belum diatur','whatsapp'=>$user->phone ?: '']);
        $otp->issue($user, $request, 'google_login'); return redirect()->route('otp.create');
    }
}
