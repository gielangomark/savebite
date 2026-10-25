<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureMerchant { public function handle(Request $request, Closure $next) { abort_unless($request->user()?->isMerchant(), 403, 'Area ini hanya untuk merchant.'); return $next($request); } }
