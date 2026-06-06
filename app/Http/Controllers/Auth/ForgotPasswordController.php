<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\Clients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Exibe o formulário de solicitação de reset de senha.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envia o link de reset de senha para o e-mail fornecido.
     * (Sobrescreve o método padrão do Laravel para lidar com Users e Clients)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->handlePasswordResetRequest($request->email);

        if ($response !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return back()->with('status', trans($response));
    }

    /**
     * Valida o e-mail da requisição.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
        ]);
    }

    /**
     * Decide qual broker (users ou clients) deve ser usado com base no e-mail.
     *
     * @param  string  $email
     * @return string
     */
    protected function handlePasswordResetRequest($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return Password::broker('users')->sendResetLink(['email' => $email]);
        }

        return Password::INVALID_USER;
    }
}