<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(\Illuminate\Http\Request $request)
    {
        $request->server->set('HTTP_HOST', $request->server->get('HTTP_X_FORWARDED_HOST'));
        $request->server->set('SSL_TLS_SNI', $request->server->get('HTTP_X_FORWARDED_HOST'));
        $request->server->set('SERVER_NAME', $request->server->get('HTTP_X_FORWARDED_HOST'));
        $request->headers->set('X_FORWARDED_HOST', $request->server->get('HTTP_X_FORWARDED_HOST'));
        $request->headers->set('HOST', $request->server->get('HTTP_X_FORWARDED_HOST'));

        // dump($request->headers);
        // dd($request->server);
        if ($request->server->has('HTTP_X_ORIGINAL_HOST')) {
            $request->server->set('HTTP_X_FORWARDED_HOST', $request->server->get('HTTP_X_ORIGINAL_HOST'));
            $request->headers->set('X_FORWARDED_HOST', $request->server->get('HTTP_X_ORIGINAL_HOST'));
        }


        if (!empty( env('NGROK_URL') ) && $request->server->has('HTTP_X_ORIGINAL_HOST')) {
            $this->app['url']->forceRootUrl(env('NGROK_URL'));
        }
    }
}
