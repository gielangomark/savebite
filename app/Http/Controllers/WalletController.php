<?php
namespace App\Http\Controllers;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
class WalletController extends Controller { public function index(WalletService $wallet) { $account=$wallet->walletFor(Auth::user()); $transactions=$account->transactions()->latest()->paginate(20); return view('wallet.index',compact('account','transactions')); } }
