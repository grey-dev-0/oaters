<?php

namespace Modules\Sapphire\App\Services;

use Exception;
use Illuminate\Support\Facades\Hash;
use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\User;

class AccountException extends Exception
{
}

class AccountManager
{
    /**
     * Provisions a platform account for the given contact and links it.
     *
     * @param  int[]  $roleIds  Role IDs granting the account its access.
     *
     * @throws AccountException Username already exists.
     */
    public static function provision(int $contactId, string $username, string $password, array $roleIds = []): void
    {
        if (User::whereUsername($username)->exists()) {
            throw new AccountException('username-taken');
        }

        $user = User::create(['username' => $username, 'password' => Hash::make($password), 'active' => true]);
        $user->assignRole($roleIds);
        Contact::whereKey($contactId)->update(['user_id' => $user->id]);
    }

    /**
     * Updates the account linked to the given contact. A null password keeps the current one.
     *
     * @param  ?string  $password
     * @param  int[]  $roleIds
     *
     * @throws AccountException No linked account or username already exists.
     */
    public static function update(int $contactId, string $username, ?string $password, array $roleIds = []): void
    {
        $user = self::linkedUser($contactId);
        if (User::whereUsername($username)->where('id', '!=', $user->id)->exists()) {
            throw new AccountException('username-taken');
        }

        $user->fill(['username' => $username] + ($password ? ['password' => Hash::make($password)] : []))->save();
        if ($roleIds) {
            $user->syncRoles($roleIds);
        }
    }

    /**
     * Suspends the account linked to the given contact.
     *
     * @throws AccountException No linked account.
     */
    public static function suspend(int $contactId): void
    {
        self::linkedUser($contactId)->fill(['active' => false])->save();
    }

    /**
     * Restores access to the account linked to the given contact.
     *
     * @throws AccountException No linked account.
     */
    public static function restore(int $contactId): void
    {
        self::linkedUser($contactId)->fill(['active' => true])->save();
    }

    /**
     * Deletes the account linked to the given contact. Used on permanent staff removal:
     * the lc_contacts.user_id foreign key is SET NULL, so nothing cascades from the
     * contact side - without this call an active orphaned login would survive.
     */
    public static function delete(int $contactId): void
    {
        User::whereKey(Contact::withTrashed()->find($contactId)?->user_id)->delete();
    }

    /**
     * Gets the account linked to the given contact.
     *
     * @throws AccountException No linked account.
     */
    private static function linkedUser(int $contactId): User
    {
        return Contact::withTrashed()->find($contactId)?->user ?: throw new AccountException('account-missing');
    }

    /**
     * Gets the username of the account linked to the given contact, if any.
     * Exposed so HR surfaces never query accounts directly.
     *
     * @return ?string
     */
    public static function username(int $contactId): ?string
    {
        return Contact::withTrashed()->find($contactId)?->user?->username;
    }

    /**
     * Gets the interface language stored on the account linked to the given
     * contact. Defensively resolves to English for unknown contacts or
     * accounts so the session-seed path never throws.
     */
    public static function locale(int $contactId): string
    {
        $locale = Contact::withTrashed()->find($contactId)?->user?->locale;

        return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
    }

    /**
     * Stores the interface language on the account linked to the given
     * contact. Values other than the supported languages are ignored; the
     * switch endpoint validates before calling.
     */
    public static function setLocale(int $contactId, string $locale): void
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            return;
        }

        Contact::withTrashed()->find($contactId)?->user?->fill(['locale' => $locale])->save();
    }
}
