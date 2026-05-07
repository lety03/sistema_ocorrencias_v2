<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta - Sistema de Ocorrências</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-glass {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-glass:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.8); }
        ::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.5); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(99, 102, 241, 0.8); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen text-slate-200 flex flex-col p-6">
    
    <!-- Navbar -->
    <nav class="glass rounded-xl px-6 py-4 mb-6 flex justify-between items-center max-w-7xl w-full mx-auto">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-500/20 w-10 h-10 rounded-full flex items-center justify-center border border-indigo-400/30">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white tracking-wide">Consulta de Dados</h1>
        </div>
        <div class="flex gap-4 items-center">
            <?php if(auth()->user()->role === 'admin'): ?>
                <a href="<?php echo e(route('upload.index')); ?>" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Upload Planilha</a>
            <?php endif; ?>

            <a href="<?php echo e(route('consulta.index')); ?>" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600/80 hover:bg-indigo-500/80 rounded-lg transition-colors border border-indigo-500/50 shadow-[0_0_15px_rgba(99,102,241,0.2)]">Consultar</a>

            <?php if(auth()->user()->role === 'admin'): ?>
                <a href="<?php echo e(route('users.index')); ?>" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Usuários</a>
                <a href="<?php echo e(route('logs.index')); ?>" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Logs</a>
            <?php endif; ?>

            <form action="<?php echo e(route('logout')); ?>" method="POST" class="ml-2">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-3 py-2 text-sm font-medium text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors border border-red-500/20">Sair</button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl w-full mx-auto flex flex-col gap-6 flex-1">

        <?php if(session('error')): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>
        
        <!-- Filtros -->
        <div class="glass rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtros de Busca
                </h2>
                <span class="text-xs px-3 py-1 rounded-full <?php echo e($totalResultados > 10000 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-700/50 text-slate-400'); ?>">
                    <?php echo e(number_format($totalResultados, 0, ',', '.')); ?> resultado(s)
                </span>
            </div>
            
            <form action="<?php echo e(route('consulta.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <div class="flex flex-col relative">
                    <label class="text-xs text-slate-400 mb-1">Nome do Funcionário</label>
                    <input type="text" id="input-nome" name="nome" value="<?php echo e(request('nome')); ?>" placeholder="Ex: João da Silva" class="input-glass rounded-lg px-3 py-2 text-sm" autocomplete="off">
                </div>
                
                <div class="flex flex-col relative">
                    <label class="text-xs text-slate-400 mb-1">Centro de Custo (CC)</label>
                    <input type="text" id="input-cc" name="cc" value="<?php echo e(request('cc')); ?>" placeholder="Ex: 1024" class="input-glass rounded-lg px-3 py-2 text-sm" autocomplete="off">
                </div>

                <div class="flex flex-col relative">
                    <label class="text-xs text-slate-400 mb-1">Tipo de Ocorrência</label>
                    <input type="text" id="input-tipo" name="tipo_ocorrencia" value="<?php echo e(request('tipo_ocorrencia')); ?>" placeholder="Ex: Faltas" class="input-glass rounded-lg px-3 py-2 text-sm" autocomplete="off">
                </div>

                <div class="flex gap-2">
                    <div class="flex flex-col w-1/2">
                        <label class="text-xs text-slate-400 mb-1">Data Início</label>
                        <input type="date" name="data_inicio" value="<?php echo e(request('data_inicio')); ?>" class="input-glass rounded-lg px-3 py-2 text-sm [color-scheme:dark]">
                    </div>
                    <div class="flex flex-col w-1/2">
                        <label class="text-xs text-slate-400 mb-1">Data Fim</label>
                        <input type="date" name="data_fim" value="<?php echo e(request('data_fim')); ?>" class="input-glass rounded-lg px-3 py-2 text-sm [color-scheme:dark]">
                    </div>
                </div>

                <div class="flex flex-col relative">
                    <label class="text-xs text-slate-400 mb-1">Filial</label>
                    <input type="text" id="input-filial" name="filial" value="<?php echo e(request('filial')); ?>" placeholder="Ex: 1" class="input-glass rounded-lg px-3 py-2 text-sm" autocomplete="off">
                </div>

                <div class="flex flex-col">
                    <label class="text-xs text-slate-400 mb-1">Duração (HH:MM)</label>
                    <input type="text" name="duracao_hhmm" value="<?php echo e(request('duracao_hhmm')); ?>" placeholder="Ex: 08:00" class="input-glass rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="flex flex-col lg:col-span-2 flex-row items-end justify-end gap-3 mt-1">
                    <a href="<?php echo e(route('consulta.index')); ?>" class="px-4 py-2 text-sm text-slate-300 hover:text-white transition-colors">Limpar Filtros</a>
                    
                    <?php if($totalResultados <= 10000): ?>
                        <a href="<?php echo e(route('consulta.exportar', request()->query())); ?>" id="btnExportar" onclick="this.classList.add('opacity-50','pointer-events-none');this.innerHTML='<svg class=\'animate-spin -ml-1 mr-2 h-4 w-4\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg>Exportando...'" class="px-4 py-2 text-sm font-medium text-indigo-400 border border-indigo-500/30 hover:bg-indigo-500/10 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Exportar Excel
                        </a>
                    <?php else: ?>
                        <span class="px-4 py-2 text-sm font-medium text-slate-500 border border-slate-600/30 rounded-lg flex items-center gap-2 cursor-not-allowed" title="Filtre para no máximo 10.000 resultados para exportar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Exportar (máx. 10.000)
                        </span>
                    <?php endif; ?>

                    <button type="submit" id="btnBuscar" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-lg flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="glass rounded-2xl shadow-xl overflow-hidden flex-1 flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-800/80 text-slate-300 uppercase text-xs tracking-wider border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Funcionário</th>
                            <th class="px-6 py-4 font-semibold">CC</th>
                            <th class="px-6 py-4 font-semibold">Tipo Ocorrência</th>
                            <th class="px-6 py-4 font-semibold">Dt Referência</th>
                            <th class="px-6 py-4 font-semibold">Início Orig.</th>
                            <th class="px-6 py-4 font-semibold">Fim Orig.</th>
                            <th class="px-6 py-4 font-semibold">Duração</th>
                            <th class="px-6 py-4 font-semibold">Total Geral</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php $__empty_1 = true; $__currentLoopData = $ocorrencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ocorrencia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-3 text-slate-400">#<?php echo e($ocorrencia->id); ?></td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-white"><?php echo e($ocorrencia->funcionario->nome); ?></div>
                                    <div class="text-xs text-slate-400">Filial: <?php echo e($ocorrencia->funcionario->filial); ?></div>
                                </td>
                                <td class="px-6 py-3 text-slate-300"><?php echo e($ocorrencia->funcionario->cc); ?></td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                        <?php echo e($ocorrencia->tipo_ocorrencia); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-300"><?php echo e($ocorrencia->dt_referencia); ?></td>
                                <td class="px-6 py-3 text-slate-400"><?php echo e($ocorrencia->inicio_origem ?: '-'); ?></td>
                                <td class="px-6 py-3 text-slate-400"><?php echo e($ocorrencia->fim_origem ?: '-'); ?></td>
                                <td class="px-6 py-3 font-medium text-emerald-400"><?php echo e($ocorrencia->duracao_hhmm ?: '-'); ?></td>
                                <td class="px-6 py-3 font-medium text-amber-400"><?php echo e($ocorrencia->total_geral ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <svg class="w-12 h-12 text-slate-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p>Nenhuma ocorrência encontrada com os filtros informados.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação Customizada para Tailwind no Blade -->
            <div class="p-4 border-t border-slate-700/50 bg-slate-800/20 mt-auto">
                <?php echo e($ocorrencias->appends(request()->query())->links('pagination::tailwind')); ?>

            </div>
        </div>
        
    </div>

    <script>
        // Loading state do botão Buscar
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btnBuscar');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Buscando...
            `;
        });

        // Função para criar o Autocomplete dinâmico
        function setupAutocomplete(inputId, campo) {
            const input = document.getElementById(inputId);
            if (!input) return;

            let timeout = null;
            
            // Container do Dropdown
            const dropdown = document.createElement('div');
            dropdown.className = 'absolute z-50 w-full bg-slate-800 border border-slate-700 rounded-lg shadow-2xl mt-1 top-full left-0 hidden max-h-60 overflow-y-auto divide-y divide-slate-700/50';
            input.parentNode.appendChild(dropdown);

            input.addEventListener('input', function() {
                clearTimeout(timeout);
                const termo = this.value.trim();
                
                if (termo.length < 2) {
                    dropdown.classList.add('hidden');
                    return;
                }

                // Debounce de 300ms para não sobrecarregar o banco
                timeout = setTimeout(async () => {
                    try {
                        const res = await fetch(`/autocomplete?campo=${campo}&termo=${encodeURIComponent(termo)}`);
                        if (!res.ok) return;
                        const dados = await res.json();
                        
                        if (dados.length > 0) {
                            dropdown.innerHTML = '';
                            dados.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-2 hover:bg-indigo-600 cursor-pointer text-sm text-slate-200 transition-colors font-medium';
                                div.textContent = item;
                                div.onclick = () => {
                                    input.value = item;
                                    dropdown.classList.add('hidden');
                                };
                                dropdown.appendChild(div);
                            });
                            dropdown.classList.remove('hidden');
                        } else {
                            dropdown.classList.add('hidden');
                        }
                    } catch(e) {
                        console.error('Erro no autocomplete', e);
                    }
                }, 300);
            });

            // Fechar dropdown ao clicar fora
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Focar input mostra dropdown se já houver pesquisa
            input.addEventListener('focus', function() {
                if(dropdown.innerHTML !== '') {
                    dropdown.classList.remove('hidden');
                }
            });
        }

        // Inicializar os campos
        setupAutocomplete('input-nome', 'nome');
        setupAutocomplete('input-cc', 'cc');
        setupAutocomplete('input-tipo', 'tipo_ocorrencia');
        setupAutocomplete('input-filial', 'filial');
    </script>
</body>
</html>
<?php /**PATH C:\Users\OI417415.OI\Documents\SDOP\sistema-ocorrencias\resources\views/consulta.blade.php ENDPATH**/ ?>