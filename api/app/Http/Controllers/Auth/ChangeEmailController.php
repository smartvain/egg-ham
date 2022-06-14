<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChangeEmailController extends Controller
{
    public function changeEmail(Request $request)
    {
        $currentPass = $request->currentPass;
        $newEmail    = $request->newEmail;
        Log::info($currentPass);
        Log::info($newEmail);
    }
}
