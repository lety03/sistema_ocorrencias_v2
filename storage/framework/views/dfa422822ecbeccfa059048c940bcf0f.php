<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - Sistema de Ocorrências</title>
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
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen text-slate-200 p-6">
    
    <nav class="glass rounded-xl px-6 py-4 mb-6 flex justify-between items-center max-w-7xl w-full mx-auto">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-white tracking-wide">Gestão de Usuários</h1>
        </div>
        <div class="flex gap-4 items-center">
            <a href="<?php echo e(route('consulta.index')); ?>" class="text-sm font-medium text-slate-300 hover:text-white">Voltar</a>
            <a href="<?php echo e(route('users.create')); ?>" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">Novo Usuário</a>
        </div>
    </nav>

    <div class="max-w-7xl w-full mx-auto">
        <?php if(session('success')): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-lg mb-6">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-xs tracking-wider border-b border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Perfil</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-white"><?php echo e($user->name); ?></td>
                            <td class="px-6 py-4 text-slate-400"><?php echo e($user->email); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold <?php echo e($user->role === 'admin' ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-500/20 text-slate-400'); ?>">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="text-indigo-400 hover:text-indigo-300">Editar</a>
                                <?php if($user->id !== auth()->id()): ?>
                                    <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" onsubmit="return confirm('Tem certeza?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-400 hover:text-red-300">Excluir</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\OI417415.OI\Documents\SDOP\sistema-ocorrencias\resources\views/users/index.blade.php ENDPATH**/ ?>