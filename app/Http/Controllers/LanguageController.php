<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        if (in_array($lang, ['en', 'id'])) {
            session()->put('locale', $lang);
            App::setLocale($lang);
        }

        return redirect()->back();
    }
}
