<?php

namespace App\Services;

use App\Models\Funcionario;
use App\Models\Ocorrencia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class PlanilhaService
{
    public function processar(string $caminhoArquivo, string $nomeOriginal, int $userId): array
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $reader = IOFactory::createReaderForFile($caminhoArquivo);
        $reader->setReadDataOnly(true);
        $planilha = $reader->load($caminhoArquivo);
        $abaAtiva = $planilha->getActiveSheet();

        // Validação do cabeçalho (Linha 3)
        $colunasEsperadas = [
            'A' => 'Coligada',
            'C' => 'Filial',
            'I' => 'Funcionário',
            'M' => 'Horario',
            'N' => 'CC_DESC',
            'O' => 'CC',
            'P' => 'Situação',
            'Q' => 'Ocorrência',
            'R' => 'Inicio Origem',
            'S' => 'Fim Origem',
            'T' => 'Dt Referência',
            'U' => 'DURACAO_HHMM',
            'V' => 'ATITUDE',
            'W' => 'Modificador Por',
            'X' => 'Total Geral',
        ];

        foreach ($colunasEsperadas as $coluna => $nomeEsperado) {
            $valorCelula = trim((string) $abaAtiva->getCell($coluna . '3')->getValue());
            if (mb_strtolower($valorCelula) !== mb_strtolower($nomeEsperado)) {
                throw new \InvalidArgumentException(
                    'As colunas do arquivo não correspondem ao formato correto. ' .
                    'Coluna ' . $coluna . '3: esperado "' . $nomeEsperado . '", encontrado "' . $valorCelula . '".'
                );
            }
        }

        // Regra: Limpar dados antes de inserir nova planilha (somente após validação do cabeçalho)
        DB::statement('PRAGMA foreign_keys = OFF;');
        Ocorrencia::truncate();
        Funcionario::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // Regra de leitura: A4 = coligada, C4 = filial
        $coligada = $abaAtiva->getCell('A4')->getValue();
        $filial = $abaAtiva->getCell('C4')->getValue();

        $maiorLinha = $abaAtiva->getHighestRow();
        
        $funcionarioAtual = null;
        $tipoAtual = null;
        
        $funcionariosCriados = 0;
        $ocorrenciasCriadas = 0;

        DB::beginTransaction();
        try {
            for ($linha = 4; $linha <= $maiorLinha; $linha++) {
                // Para performance, lê os valores diretamente para não instanciar celulas pesadas
                $valoresLinha = $abaAtiva->rangeToArray('A' . $linha . ':X' . $linha, null, false, false)[0];
                
                $linhaVazia = true;
                foreach ($valoresLinha as $valor) {
                    if ($valor !== null && $valor !== '') {
                        $linhaVazia = false;
                        break;
                    }
                }

            if ($linhaVazia) {
                continue;
            }

            // O rangeToArray retorna array indexado de 0 a 23 (A a X)
            $nome = $valoresLinha[8]; // Coluna I (9ª coluna -> index 8)
            $horario = $valoresLinha[12]; // Coluna M (13ª -> index 12)
            $ccDesc = $valoresLinha[13]; // Coluna N
            $cc = $valoresLinha[14]; // Coluna O
            $situacao = $valoresLinha[15]; // Coluna P

            // Lógica para manter funcionarioAtual
            if (!empty($nome)) {
                $funcionarioAtual = Funcionario::create([
                    'coligada' => $coligada,
                    'filial' => $filial,
                    'nome' => $nome,
                    'horario' => $horario,
                    'cc_desc' => $ccDesc,
                    'cc' => $cc,
                    'situacao' => $situacao,
                ]);
                $funcionariosCriados++;
            }

            // Não processa ocorrência se não tivermos um funcionário
            if (!$funcionarioAtual) {
                continue;
            }

            $tipoOcorrencia = $valoresLinha[16]; // Coluna Q (17ª -> index 16)
            
            // Lógica para manter tipoAtual
            if (!empty($tipoOcorrencia)) {
                $tipoAtual = $tipoOcorrencia;
            }

            $inicioOrigem = $valoresLinha[17]; // Coluna R
            $fimOrigem = $valoresLinha[18]; // Coluna S

            // Regra: Somente insere ocorrência se:
            // - tipoAtual existe
            // - AND (inicio_origem OR fim_origem está preenchido)
            if ($tipoAtual && (!empty($inicioOrigem) || !empty($fimOrigem))) {
                Ocorrencia::create([
                    'funcionario_id' => $funcionarioAtual->id,
                    'tipo_ocorrencia' => $tipoAtual,
                    'inicio_origem' => $inicioOrigem,
                    'fim_origem' => $fimOrigem,
                    'dt_referencia' => $valoresLinha[19], // T
                    'duracao_hhmm' => $valoresLinha[20], // U
                    'atitude' => $valoresLinha[21], // V
                    'modificador_por' => $valoresLinha[22], // W
                    'total_geral' => $valoresLinha[23], // X
                ]);
                $ocorrenciasCriadas++;
            }
        }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log de erro fatal no processamento
            DB::table('logs_importacao')->insert([
                'nome_arquivo' => $nomeOriginal,
                'user_id' => $userId,
                'linhas_sucesso' => 0,
                'linhas_erro' => 1,
                'detalhes_erros' => json_encode(['erro_fatal' => $e->getMessage()]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            throw $e;
        }

        // Log de sucesso
        DB::table('logs_importacao')->insert([
            'nome_arquivo' => $nomeOriginal,
            'user_id' => $userId,
            'linhas_sucesso' => $ocorrenciasCriadas,
            'linhas_erro' => 0,
            'detalhes_erros' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'funcionarios' => $funcionariosCriados,
            'ocorrencias' => $ocorrenciasCriadas
        ];
    }
}
