<?php

namespace App\Http\Controllers;

use App\Services\PlanilhaService;
use Illuminate\Http\Request;
use Throwable;

class UploadController extends Controller
{
    protected PlanilhaService $planilhaService;

    public function __construct(PlanilhaService $planilhaService)
    {
        $this->planilhaService = $planilhaService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|extensions:xls,xlsx|max:10240',
        ], [
            'arquivo.required' => 'O arquivo é obrigatório.',
            'arquivo.file' => 'O arquivo enviado não é válido.',
            'arquivo.mimes' => 'Apenas arquivos Excel (.xls, .xlsx) são permitidos.',
        ]);

        try {
            $resultado = $this->planilhaService->processar(
                $request->file('arquivo')->getRealPath(),
                $request->file('arquivo')->getClientOriginalName(),
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Planilha processada com sucesso!',
                'data' => [
                    'total_funcionarios' => $resultado['funcionarios'],
                    'total_ocorrencias' => $resultado['ocorrencias']
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a planilha.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
