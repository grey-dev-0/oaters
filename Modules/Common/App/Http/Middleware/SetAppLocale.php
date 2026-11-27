<?php

namespace Modules\Common\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Common\App\Contracts\LocalePreference;

/**
 * Resolves the active interface language for every web request: the session
 * working copy wins; when absent and a user is authenticated, it is seeded
 * once from the account-stored preference (via the LocalePreference
 * contract) and cached in the session; guests default to English.
 *
 * Guests never carry a preference: a working copy left over from a signed
 * out session is dropped so the login page stays English (FR-010) and the
 * next sign-in re-seeds from the account-stored preference (Q5=A).
 */
class SetAppLocale
{
    public function __construct(private readonly LocalePreference $preferences)
    {
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user() === null) {
            $request->session()->forget('locale');
            app()->setLocale('en');

            return $next($request);
        }

        $locale = $request->session()->get('locale');

        if ($locale === null) {
            $locale = $this->preferences->current($request) ?? 'en';
            $request->session()->put(['locale' => $locale]);
        }

        app()->setLocale(in_array($locale, ['en', 'ar'], true) ? $locale : 'en');

        return $next($request);
    }
}
