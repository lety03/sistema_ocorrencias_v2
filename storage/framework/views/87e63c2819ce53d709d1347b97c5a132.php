<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Importação - Sistema de Ocorrências</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen text-slate-200 p-6">
    
    <nav class="glass rounded-xl px-6 py-4 mb-6 flex justify-between items-center max-w-7xl w-full mx-auto">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-white tracking-wide">Logs de Importação</h1>
        </div>
        <div class="flex gap-4 items-center">
            <a href="<?php echo e(route('consulta.index')); ?>" class="text-sm font-medium text-slate-300 hover:text-white">Voltar</a>
        </div>
    </nav>

    <div class="max-w-7xl w-full mx-auto">
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-xs tracking-wider border-b border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Arquivo</th>
                        <th class="px-6 py-4">Usuário</th>
                        <th class="px-6 py-4">Sucesso</th>
                        <th class="px-6 py-4">Erro</th>
                        <th class="px-6 py-4">Data</th>
                        <th class="px-6 py-4">Detalhes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-white"><?php echo e($log->nome_arquivo); ?></td>
                            <td class="px-6 py-4 text-slate-300"><?php echo e($log->user_name); ?></td>
                            <td class="px-6 py-4 text-emerald-400 font-bold"><?php echo e($log->linhas_sucesso); ?></td>
                            <td class="px-6 py-4 text-red-400 font-bold"><?php echo e($log->linhas_erro); ?></td>
                            <td class="px-6 py-4 text-slate-400 text-xs"><?php echo e(date('d/m/Y H:i:s', strtotime($log->created_at))); ?></td>
                            <td class="px-6 py-4 text-slate-400 text-xs max-w-xs truncate">
                                <?php echo e($log->detalhes_erros); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-500">Nenhum log encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/leticia/Documentos/sistema-ocorrencias/resources/views/logs/index.blade.php ENDPATH**/ ?>