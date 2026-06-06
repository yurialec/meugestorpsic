<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Crm\Client\CreateClientRequest;
use App\Http\Traits\TraitSite;
use App\Models\Admin\Plan;
use App\Models\Tenants\Tenant;
use App\Services\Admin\ClientsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    use TraitSite;

    protected $clientService;

    public function __construct(ClientsService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $mainText = $this->mainText();
        $carousels = $this->carousels();
        $contact = $this->contactSite();
        $socialmedias = $this->socialmedias();
        $about = $this->aboutSite();
        $plans = $this->plans();
        
        return view('site', compact('mainText', 'carousels', 'contact', 'socialmedias', 'about', 'plans'));
    }

    public function about()
    {
        $about = $this->aboutSite();
        return view('partials.about.index', compact('about'));
    }

    public function contact()
    {
        $contact = $this->contactSite();
        return view('partials.contact.index', compact('contact'));
    }

    public function formFacaParte()
    {
        return view('partials.facaParte.index');
    }

    public function facaParte(CreateClientRequest $request)
    {
        $result = $this->clientService->create($request->all());

        if ($result['status']) {
            try {
                $client = $result['client'];

                Auth::guard('client')->login($client);
                $request->session()->regenerate();

                if (!$client->relationLoaded('tenant')) {
                    $client->load('tenant');
                }

                $tenant = Tenant::find($client->tenant_id);

                session([
                    'tenant_domain' => $tenant->domain,
                    'tenant_id' => $tenant->id,
                    'tenant' => $tenant->toArray(),
                    'is_admin' => true,
                ]);

                session()->put('client', [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'cpf' => $client->cpf ?? null,
                    'crp' => $client->crp ?? null,
                    'function' => $client->function ?? null,
                    'phone' => $client->phone ?? null,
                ]);

                return redirect()->intended(
                    route('tenant.dashboard', ['tenant' => session('tenant_domain')])
                );

            } catch (Exception $err) {
                Auth::guard('client')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                Log::error('ERRO AO REALIZAR LOGIN', ['erro' => $err->getMessage()]);
                return back()->withErrors(['login' => 'Ocorreu um erro ao processar o login. Por favor, tente novamente.']);
            }
        }

        return redirect()->back()->withErrors('error', 'Ocorreu um erro ao processar seu cadastro.');
    }
}
