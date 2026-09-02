<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L\'adresse e-mail professionnelle est obligatoire.',
            'email.email'    => 'Veuillez saisir une adresse e-mail valide.',
        ]);

        try {
            // Tentative d'envoi du lien sécurisé
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                        ? back()->with('status', __($status))
                        : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Password reset mail failure: ' . $e->getMessage());

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Le service d\'envoi d\'e-mail a rencontré une indisponibilité temporaire. Veuillez réessayer ou contacter l\'administrateur.']);
        }
    }
}
