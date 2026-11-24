<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request, AccountService $service): RedirectResponse
    {
        $service->markEmailAsVerified($request->user());

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
