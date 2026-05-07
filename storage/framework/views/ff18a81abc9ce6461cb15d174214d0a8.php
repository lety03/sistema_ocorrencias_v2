<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($user) ? 'Editar' : 'Novo'); ?> Usuário - Sistema de Ocorrências</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-glass {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen text-slate-200 flex items-center justify-center p-6">
    
    <div class="glass rounded-2xl p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-white mb-6"><?php echo e(isset($user) ? 'Editar' : 'Criar Novo'); ?> Usuário</h2>
        
        <form action="<?php echo e(isset($user) ? route('users.update', $user) : route('users.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php if(isset($user)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Nome</label>
                <input type="text" name="name" value="<?php echo e(old('name', $user->name ?? '')); ?>" class="input-glass w-full rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Email</label>
                <input type="email" name="email" value="<?php echo e(old('email', $user->email ?? '')); ?>" class="input-glass w-full rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Senha <?php echo e(isset($user) ? '(deixe em branco para não alterar)' : ''); ?></label>
                <input type="password" name="password" class="input-glass w-full rounded-lg px-4 py-2" <?php echo e(isset($user) ? '' : 'required'); ?>>
            </div>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Perfil de Acesso</label>
                <select name="role" class="input-glass w-full rounded-lg px-4 py-2 bg-slate-900" required>
                    <option value="visualizador" <?php echo e((old('role', $user->role ?? '') == 'visualizador') ? 'selected' : ''); ?>>Visualizador (Somente Consulta)</option>
                    <option value="admin" <?php echo e((old('role', $user->role ?? '') == 'admin') ? 'selected' : ''); ?>>Administrador (Acesso Total)</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="<?php echo e(route('users.index')); ?>" class="flex-1 px-4 py-2 text-center text-sm font-medium text-slate-400 hover:text-white transition-colors">Cancelar</a>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Salvar
                </button>
            </div>
        </form>
    </div>

</body>
</html>
<?php /**PATH C:\Users\OI417415.OI\Documents\SDOP\sistema-ocorrencias\resources\views/users/form.blade.php ENDPATH**/ ?>