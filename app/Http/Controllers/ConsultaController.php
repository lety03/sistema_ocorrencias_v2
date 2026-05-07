<?php

namespace App\Http\Controllers;

use App\Services\ConsultaService;
use App\Models\Funcionario;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    protected ConsultaService $consultaService;

    public function __construct(ConsultaService $consultaService)
    {
        $this->consultaService = $consultaService;
    }

    public function index(Request $request)
    {
        $filtros = $request->only([
            'nome', 
            'cc', 
            'filial',
            'tipo_ocorrencia', 
            'data_inicio', 
            'data_fim',
            'duracao_hhmm',
            'atitude',
            'modificador_por'
        ]);

        $ocorrencias = $this->consultaService->filtrar($filtros);
        $totalResultados = $this->consultaService->contar($filtros);

        return view('consulta', [
            'ocorrencias' => $ocorrencias,
            'filtros' => $filtros,
            'totalResultados' => $totalResultados
        ]);
    }

    public function exportar(Request $request)
    {
        $filtros = $request->only([
            'nome', 
            'cc', 
            'filial',
            'tipo_ocorrencia', 
            'data_inicio', 
            'data_fim',
            'duracao_hhmm',
            'atitude',
            'modificador_por'
        ]);

        $total = $this->consultaService->contar($filtros);

        if ($total > 10000) {
            return redirect()->route('consulta.index', $filtros)
                ->with('error', 'Não é possível exportar mais de 10.000 registros. Aplique filtros para reduzir o volume de dados.');
        }

        $writer = $this->consultaService->gerarExcel($filtros);

        $filename = 'ocorrencias_exportadas_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function autocomplete(Request $request)
    {
        $campo = $request->query('campo');
        $termo = $request->query('termo');

        if (!$campo || !$termo) {
            return response()->json([]);
        }

        $resultados = [];

        if (in_array($campo, ['nome', 'cc', 'filial'])) {
            $resultados = Funcionario::where($campo, 'like', '%' . $termo . '%')
                ->whereNotNull($campo)
                ->select($campo)
                ->distinct()
                ->limit(10)
                ->pluck($campo);
        } elseif ($campo === 'tipo_ocorrencia') {
            $resultados = Ocorrencia::where($campo, 'like', '%' . $termo . '%')
                ->whereNotNull($campo)
                ->select($campo)
                ->distinct()
                ->limit(10)
                ->pluck($campo);
        }

        return response()->json($resultados);
    }
}
