public function handle($request, Closure $next)
{
    if (!auth()->user() || !auth()->user()->email_verified_at) {
        return redirect()->route('otp.form');
    }

    return $next($request);
}
