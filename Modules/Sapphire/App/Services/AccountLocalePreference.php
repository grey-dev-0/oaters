<?php

namespace Modules\Sapphire\App\Services;

use Illuminate\Http\Request;
use Modules\Common\App\Contracts\LocalePreference;

/**
 * Common-facing adapter over the account boundary: resolves and stores the
 * interface-language preference through AccountManager so no other module
 * ever queries accounts directly.
 */
class AccountLocalePreference implements LocalePreference
{
    /**
     * {@inheritDoc}
     */
    public function current(Request $request): ?string
    {
        $user = $request->user();

        if ($user === null) {
            return null;
        }

        // Queried through the relation (not the magic attribute) on purpose:
        // seeding must not cache the contact relation on the shared user
        // instance before later code links one.
        $contact = $user->contact()->first();

        return $contact === null ? null : AccountManager::locale($contact->id);
    }

    /**
     * {@inheritDoc}
     */
    public function update(Request $request, string $locale): void
    {
        $user = $request->user();

        if ($user !== null && ($contact = $user->contact()->first()) !== null) {
            AccountManager::setLocale($contact->id, $locale);
        }
    }
}
