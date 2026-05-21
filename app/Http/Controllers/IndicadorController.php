<?php

namespace App\Http\Controllers;

use App\Models\Ocorrencia;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicadorController extends Controller
{
    /**
     * Exibe a tela de Indicadores / Rankings.
     * Carrega os tipos de ocorrência com suas contagens totais.
     */
    public function index()
    {
        $tipos = Ocorrencia::select('tipo_ocorrencia', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tipo_ocorrencia')
            ->where('tipo_ocorrencia', '!=', '')
            ->groupBy('tipo_ocorrencia')
            ->orderByDesc('total')
            ->get();

        return view('indicadores', [
            'tipos' => $tipos,
        ]);
    }

    /**
     * Retorna o ranking de funcionários para um tipo de ocorrência (AJAX).
     * Suporta paginação via query params: page, per_page.
     */
    public function ranking(Request $request)
    {
        $tipo = $request->query('tipo');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;

        if (!$tipo) {
            return response()->json([]);
        }

        // Busca todos os funcionários agrupados e ordenados
        $allRanking = Ocorrencia::select(
            'funcionario_id',
            DB::raw('COUNT(*) as total_ocorrencias')
        )
            ->where('tipo_ocorrencia', $tipo)
            ->groupBy('funcionario_id')
            ->orderByDesc('total_ocorrencias')
            ->with('funcionario:id,nome,filial,cc_desc,cc,coligada')
            ->get()
            ->filter(fn($item) => $item->funcionario !== null)
            ->values();

        // Calcula a posição (ranking) considerando empates
        $currentRank = 1;
        $previousTotal = -1;
        $realIndex = 1;

        foreach ($allRanking as $item) {
            if ($previousTotal !== -1 && $item->total_ocorrencias < $previousTotal) {
                $currentRank = $realIndex;
            }
            $item->posicao = $currentRank;
            $previousTotal = $item->total_ocorrencias;
            $realIndex++;
        }

        $totalFuncionarios = $allRanking->count();
        $totalPages = max(1, (int) ceil($totalFuncionarios / $perPage));
        $page = min($page, $totalPages);

        // Valor máximo global (primeiro do ranking) para calcular % das barras
        $max = $allRanking->isNotEmpty() ? $allRanking->first()->total_ocorrencias : 1;

        // Fatia a página atual
        $offset = ($page - 1) * $perPage;
        $pageItems = $allRanking->slice($offset, $perPage)->values();

        $ranking = $pageItems->map(function ($item) {
            $cc_desc = $item->funcionario->cc_desc ?? '';
            $cc = $item->funcionario->cc ?? '';
            $centroCusto = trim($cc_desc) && trim($cc)
                ? $cc_desc . ' - ' . $cc
                : ($cc_desc ?: $cc ?: '-');

            return [
                'posicao' => $item->posicao,
                'nome' => $item->funcionario->nome,
                'filial' => $item->funcionario->filial,
                'centro_custo' => $centroCusto,
                'total_ocorrencias' => $item->total_ocorrencias,
            ];
        });

        return response()->json([
            'ranking' => $ranking,
            'total_funcionarios' => $totalFuncionarios,
            'max' => $max,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ]);
    }
}
