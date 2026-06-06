<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ImportationModelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Crm\Client\CreateClientRequest;
use App\Http\Requests\Admin\Crm\Client\UpdateClientRequest;
use App\Services\Admin\ClientsService;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    protected $ClientsService;

    public function __construct(ClientsService $ClientsService)
    {
        $this->ClientsService = $ClientsService;
    }

    public function index()
    {
        return view('admin.crm.clients.index');
    }

    public function list(Request $request)
    {
        $clients = $this->ClientsService->all();

        if ($clients) {
            return response()->json([
                'status' => true,
                'clients' => $clients
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function create()
    {
        return view('admin.crm.clients.create');
    }

    public function store(CreateClientRequest $request)
    {
        $client = $this->ClientsService->create($request->all());

        if ($client) {
            return response()->json([
                true,
            ], 200);
        } else {
            return response()->json([
                false
            ], 400);
        }
    }

    public function edit($id)
    {
        return view('admin.crm.clients.edit', compact('id'));
    }

    public function find($id)
    {
        $client = $this->ClientsService->find($id);
        if ($client) {
            return response()->json([
                'status' => true,
                'client' => $client,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao localizar cliente'
            ], 50);
        }
    }

    public function update($id, UpdateClientRequest $request)
    {
        $client = $this->ClientsService->update($id, $request->all());

        if ($client) {
            return response()->json([
                true
            ], 200);
        } else {
            return response()->json([
                false,
            ], 500);
        }
    }

    public function delete($id)
    {
        $client = $this->ClientsService->delete($id);

        if ($client) {
            return response()->json([
                true,
            ], 200);
        } else {
            return response()->json([
                false,
            ], 500);
        }
    }

    public function downloadImportationModel()
    {
        $export = new ImportationModelExport();
        return $export->download('modelo_clientes.xlsx');
    }
}