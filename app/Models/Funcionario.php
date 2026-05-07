<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'coligada',
        'filial',
        'nome',
        'horario',
        'cc_desc',
        'cc',
        'situacao',
    ];

    public function ocorrencias()
    {
        return $this->hasMany(Ocorrencia::class);
    }
}
