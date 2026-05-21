<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ocorrencia extends Model
{
    use HasFactory;
    //Tabela ocorrencias
    protected $fillable = [
        'funcionario_id',
        'tipo_ocorrencia',
        'inicio_origem',
        'fim_origem',
        'dt_referencia',
        'duracao_hhmm',
        'atitude',
        'modificador_por',
        'total_geral',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}
