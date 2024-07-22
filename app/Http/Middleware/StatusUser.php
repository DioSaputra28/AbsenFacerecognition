<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StatusUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            // Cek status pengguna
            if (Auth::user()->status) {
                return $next($request);
            } else {
                // Menyimpan pesan ke session
                Session::flash('warning', 'Akun anda belum diverifikasi atau telah ditangguhkan, silahkan cek email anda.');

                // Logout pengguna
                Auth::logout();

                // Redirect ke halaman sebelumnya
                return redirect()->back();
            }
        }

        // Jika pengguna tidak login, lanjutkan request
        return $next($request);
    }
}
