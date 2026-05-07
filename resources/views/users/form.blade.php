<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? 'Editar' : 'Novo' }} Usuário - Sistema de Ocorrências</title>
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
        <h2 class="text-2xl font-bold text-white mb-6">{{ isset($user) ? 'Editar' : 'Criar Novo' }} Usuário</h2>
        
        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div>
                <label class="text-xs text-slate-400 block mb-1">Nome</label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="input-glass w-full rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="input-glass w-full rounded-lg px-4 py-2" required>
            </div>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Senha {{ isset($user) ? '(deixe em branco para não alterar)' : '' }}</label>
                <input type="password" name="password" class="input-glass w-full rounded-lg px-4 py-2" {{ isset($user) ? '' : 'required' }}>
            </div>

            <div>
                <label class="text-xs text-slate-400 block mb-1">Perfil de Acesso</label>
                <select name="role" class="input-glass w-full rounded-lg px-4 py-2 bg-slate-900" required>
                    <option value="visualizador" {{ (old('role', $user->role ?? '') == 'visualizador') ? 'selected' : '' }}>Visualizador (Somente Consulta)</option>
                    <option value="admin" {{ (old('role', $user->role ?? '') == 'admin') ? 'selected' : '' }}>Administrador (Acesso Total)</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('users.index') }}" class="flex-1 px-4 py-2 text-center text-sm font-medium text-slate-400 hover:text-white transition-colors">Cancelar</a>
                <button type="submit" id="btnSalvar" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    Salvar
                </button>
            </div>
        </form>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btnSalvar');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Salvando...
            `;
        });
    </script>
</body>
</html>
