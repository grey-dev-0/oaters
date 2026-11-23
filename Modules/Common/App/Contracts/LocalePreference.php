<?php

namespace Modules\Common\App\Contracts;

use Illuminate\Http\Request;

/**
 * The per-account interface-language preference, accessed through the
 * account boundary so Common never touches account storage directly
 * (constitution Principle III: Lava depends on no business module).
 *
 * Sapphire provides the implementation; Common's locale mechanism consumes
 * this contract via the container.
 */
interface LocalePreference
{
    /**
     * Resolves the stored preference of the authenticated account.
     * Returns null when the request has no linked contact to key by;
     * callers fall back to the default language.
     */
    public function current(Request $request): ?string;

    /**
     * Persists the preference for the authenticated account. Implementations
     * ignore locales outside the supported set.
     */
    public function update(Request $request, string $locale): void;
}
