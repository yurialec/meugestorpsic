<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Mail\TenantRecoverPassEmail;
use App\Models\Admin\Clients;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthTenantForgotPasswordController extends Controller
{
    public function showEmailForm()
    {
        return view('tenant.auth.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|max:255|email',
        ]);

        $client = Clients::where('email', $validated['email'])->first();

        if (empty($client)) {
            $msg = 'E-mail não localizado';
            return view('partials.notfound.index', compact('msg'));
        }

        try {
            $client->token = Str::random(40);
            $client->save();

            Mail::to($client->email)
                ->send(new TenantRecoverPassEmail($client));

            return redirect()->back()->with('success', 'Um e-mail foi enviado contendo o link para redefinição de senha.');
        } catch (Exception $err) {
            Log::error('Erro ao enviar email para recuperar senha do cliente', [$err->getMessage()]);

            return redirect()->back()->withErrors(['message' => 'Erro ao enviar e-mail para recuperar senha.']);
        }
    }

    public function showResetForm($token)
    {
        $client = Clients::where('token', $token)->first();

        if (empty($client)) {
            $msg = 'A página que você está procurando não foi encontrada.';
            return view('partials.notfound.index', compact('msg'));
        }

        $token = $client->token;
        $email = $client->email;
        return view('tenant.auth.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|max:255|email',
            'password' => 'required_with:password_confirmation|same:password_confirmation|string|min:6|regex:/[a-zA-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
            'password_confirmation' => 'min:6|regex:/[a-zA-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
        ]);

        $client = Clients::where('token', $request->token)
            ->where('email', $validated['email'])
            ->first();

        if (empty($client)) {
            $msg = 'A página que você está procurando não foi encontrada.';
            return view('partials.notfound.index', compact('msg'));
        }

        try {
            $client->password = Hash::make($request->password);
            $client->token = null;
            $client->save();

            $credentials = [
                'email' => $client->email,
                'password' => $client->password,
            ];

            Auth::guard('client')->attempt($credentials);
            $user = Auth::guard('client')->user();
            session([
                'user' => $user,
                'tenant_domain' => $client->tenant->domain,
                'tenant_id' => $client->tenant->id,
            ]);

            return redirect()->route('tenant.dashboard', ['tenant' => session('tenant_domain')]);
        } catch (Exception $err) {
            Log::error('Erro ao redefinir senha do cliente', [$err->getMessage()]);
        }
    }
}
