<?php

namespace Modules\Common\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Common\App\Contracts\LocalePreference;

class LocaleController extends Controller
{
    public function __construct(private readonly LocalePreference $preferences)
    {
    }

    /**
     * Switches the interface language: validates the requested locale,
     * writes the session working copy and the account-stored preference
     * together, then reloads the page the user switched from.
     */
    public function switch(Request $request)
    {
        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in(['en', 'ar'])],
        ]);

        $request->session()->put(['locale' => $data['locale']]);
        $this->preferences->update($request, $data['locale']);

        return redirect()->back($status = 302);
    }
}
