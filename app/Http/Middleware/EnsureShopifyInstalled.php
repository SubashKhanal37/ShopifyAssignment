<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopifyInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        $shopDomain = $request->query('shop')
            ?? $request->header('X-Shop-Domain')
            ?? session('shopify_domain')
            ?? $request->cookie('shopify_domain');

        if (!$shopDomain) {
            $singleShop = User::whereNotNull('password')->first();
            if ($singleShop) {
                $shopDomain = $singleShop->name;
            }
        }

        if (!$shopDomain) {
            return redirect()->route('login');
        }

        $shopDomain = $this->normalizeDomain($shopDomain);

        $shop = User::where('name', $shopDomain)->first();

        if (!$shop || !$shop->password) {
            session(['shopify_domain' => $shopDomain]);
            Cookie::queue('shopify_domain', $shopDomain, 60 * 24, '/', null, true, false, false, 'none');
            return redirect()->route('authenticate', ['shop' => $shopDomain]);
        }

        session(['shopify_domain' => $shopDomain]);
        Cookie::queue('shopify_domain', $shopDomain, 60 * 24, '/', null, true, false, false, 'none');
        Auth::login($shop);

        return $next($request);
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = rtrim($domain, '/');
        return $domain;
    }
}
