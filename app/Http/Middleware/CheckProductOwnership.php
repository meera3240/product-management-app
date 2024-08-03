<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProductOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $product = $request->route('product');
        if (auth()->user()->hasRole('sub-admin') && $product->user_id != auth()->id()) {
            return redirect()->route('products.index')->with('error', 'Unauthorized action.');
        }
        return $next($request);
    }
}
