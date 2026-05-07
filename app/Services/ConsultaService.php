<?php

namespace App\Services;

use App\Models\Ocorrencia;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Database\Eloquent\Builder;

class ConsultaService
{
    private function buildQuery(array $filtros): Builder
    {
        $query = Ocorrencia::with('funcionario');

        if (!empty($filtros['tipo_ocorrencia'])) {
            $query->where('tipo_ocorrencia', 'like', '%' . $filtros['tipo_ocorrencia'] . '%');
        }

        if (!empty($filtros['data_inicio'])) {
            $query->where('dt_referencia', '>=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query->where('dt_referencia', '<=', $filtros['data_fim']);
        }

        if (!empty($filtros['duracao_hhmm'])) {
            $query->where('duracao_hhmm', 'like', '%' . $filtros['duracao_hhmm'] . '%');
        }

        if (!empty($filtros['atitude'])) {
            $query->where('atitude', 'like', '%' . $filtros['atitude'] . '%');
        }

        if (!empty($filtros['modificador_por'])) {
            $query->where('modificador_por', 'like', '%' . $filtros['modificador_por'] . '%');
        }

        if (!empty($filtros['nome']) || !empty($filtros['cc']) || !empty($filtros['filial'])) {
            $query->whereHas('funcionario', function ($q) use ($filtros) {
                if (!empty($filtros['nome'])) {
                    $q->where('nome', 'like', '%' . $filtros['nome'] . '%');
                }
                
                if (!empty($filtros['cc'])) {
                    $q->where('cc', 'like', '%' . $filtros['cc'] . '%');
                }

                if (!empty($filtros['filial'])) {
                    $q->where('filial', 'like', '%' . $filtros['filial'] . '%');
                }
            });
        }

        return $query->orderBy('id', 'desc');
    }

    public function filtrar(array $filtros): LengthAwarePaginator
    {
        return $this->buildQuery($filtros)->paginate(20);
    }

    public function contar(array $filtros): int
    {
        return $this->buildQuery($filtros)->count();
    }

    public function gerarExcel(array $filtros)
    {
        $ocorrencias = $this->buildQuery($filtros)->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Cabeçalho
        $headers = [
            'ID', 'Funcionário', 'Filial', 'CC', 'Tipo Ocorrência', 
            'Dt Referência', 'Início Origem', 'Fim Origem', 
            'Duração', 'Atitude', 'Modificador Por', 'Total Geral'
        ];
        
        $sheet->fromArray($headers, NULL, 'A1');
        
        $linha = 2;
        foreach ($ocorrencias as $oc) {
            $data = [
                $oc->id,
                $oc->funcionario->nome,
                $oc->funcionario->filial,
                $oc->funcionario->cc,
                $oc->tipo_ocorrencia,
                $oc->dt_referencia,
                $oc->inicio_origem,
                $oc->fim_origem,
                $oc->duracao_hhmm,
                $oc->atitude,
                $oc->modificador_por,
                $oc->total_geral
            ];
            $sheet->fromArray($data, NULL, 'A' . $linha);
            $linha++;
        }

        $writer = new Xlsx($spreadsheet);
        
        // Retornar stream para o controller
        return $writer;
    }
}
