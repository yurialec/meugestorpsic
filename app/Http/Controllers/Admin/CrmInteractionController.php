<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Crm\Interaction\CreateInsteractionRequest;
use App\Services\Admin\CrmInteractionService;
use Illuminate\Http\Request;
use Storage;

class CrmInteractionController extends Controller
{
    protected $CrmInteractionService;

    public function __construct(CrmInteractionService $CrmInteractionService)
    {
        $this->CrmInteractionService = $CrmInteractionService;
    }

    public function index()
    {
        return view('admin.crm.interaction.index');
    }

    public function list(Request $request)
    {
        $interactions = $this->CrmInteractionService->all();

        if ($interactions) {
            return response()->json([
                'status' => true,
                'interactions' => $interactions
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function downloadFile(Request $request)
    {
        $data = $request->interaction;
        $filePath = $data['attachment'];
        if (!Storage::exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::download($filePath);
    }

    public function store(CreateInsteractionRequest $request)
    {
        $interaction = $this->CrmInteractionService->create($request->all());

        if ($interaction) {
            return response()->json([
                'status' => true,
                'interaction' => $interaction
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function edit($id)
    {
        return view('admin.crm.interaction.edit', compact('id'));
    }

    public function find($id)
    {
        $interaction = $this->CrmInteractionService->find($id);

        if ($interaction) {
            return response()->json([
                'interaction' => $interaction,
            ], 200);
        } else {
            return response()->json([
                false
            ], 500);
        }
    }
}