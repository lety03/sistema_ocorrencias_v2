<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ocorrências do Ponto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen text-slate-200 flex flex-col p-6">

    <!-- Navbar -->
    <nav
        class="glass rounded-xl px-6 py-4 mb-6 flex justify-between items-center max-w-7xl w-full mx-auto transition-all">
        <div class="flex items-center gap-3">
            <div
                class="bg-indigo-500/20 w-10 h-10 rounded-full flex items-center justify-center border border-indigo-400/30">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white tracking-wide">Upload de Dados</h1>
        </div>
        <div class="flex gap-4 items-center">
            <a href="{{ route('upload.index') }}"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600/80 hover:bg-indigo-500/80 rounded-lg transition-colors border border-indigo-500/50 shadow-[0_0_15px_rgba(99,102,241,0.2)]">Upload
                Planilha</a>

            <a href="{{ route('consulta.index') }}"
                class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Consultar</a>

            <a href="{{ route('indicadores.index') }}"
                class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Indicadores</a>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Usuários</a>
                <a href="{{ route('logs.index') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Logs</a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="ml-2">
                @csrf
                <button type="submit"
                    class="px-3 py-2 text-sm font-medium text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors border border-red-500/20">Sair</button>
            </form>
        </div>
    </nav>

    <div class="flex flex-col items-center justify-center flex-1 w-full">
        <div class="glass rounded-2xl shadow-2xl p-8 max-w-lg w-full animate-fade-in transition-all duration-300">
            <div class="text-center mb-8">
                <div
                    class="bg-indigo-500/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-indigo-400/30">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Sistema de Ocorrências</h1>
                <p class="text-slate-400 text-sm">Faça o upload da planilha Excel (.xls, .xlsx) para sincronizar os
                    funcionários e ocorrências do ponto.</p>
            </div>

            <form id="uploadForm" class="space-y-6" action="{{ route('upload.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="relative group">
                    <label for="arquivo"
                        class="flex flex-col items-center justify-center w-full h-40 border-2 border-slate-600 border-dashed rounded-xl cursor-pointer bg-slate-800/50 hover:bg-slate-700/50 hover:border-indigo-500 transition-all duration-200 group-hover:shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-3 text-slate-400 group-hover:text-indigo-400 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="mb-1 text-sm text-slate-300"><span class="font-semibold">Clique para
                                    selecionar</span> ou arraste o arquivo</p>
                            <p class="text-xs text-slate-500">Apenas arquivos .XLS ou .XLSX</p>
                            <p id="fileName" class="mt-3 text-sm font-medium text-indigo-400 hidden"></p>
                        </div>
                        <input id="arquivo" name="arquivo" type="file" class="hidden" accept=".xls,.xlsx" required />
                    </label>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span>Processar Planilha</span>
                </button>
            </form>

            <div id="resultado" class="mt-6 hidden animate-fade-in">
                <div class="rounded-lg bg-green-500/10 border border-green-500/20 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-400" id="resTitulo">Sucesso!</h3>
                            <div class="mt-2 text-sm text-green-200/80">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Funcionários processados: <span class="font-bold text-white"
                                            id="resFunc">0</span></li>
                                    <li>Ocorrências registradas: <span class="font-bold text-white"
                                            id="resOcor">0</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="erro" class="mt-6 hidden animate-fade-in">
                <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-400">Erro ao processar</h3>
                            <div class="mt-1 text-sm text-red-200/80" id="resErroMsg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const fileInput = document.getElementById('arquivo');
            const fileName = document.getElementById('fileName');
            const form = document.getElementById('uploadForm');
            const submitBtn = document.getElementById('submitBtn');
            const resultadoDiv = document.getElementById('resultado');
            const erroDiv = document.getElementById('erro');

            fileInput.addEventListener('change', function (e) {
                if (e.target.files.length > 0) {
                    fileName.textContent = e.target.files[0].name;
                    fileName.classList.remove('hidden');
                } else {
                    fileName.classList.add('hidden');
                }
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!fileInput.files.length) return;

                const formData = new FormData(this);

                // UI Loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processando...
            `;
                resultadoDiv.classList.add('hidden');
                erroDiv.classList.add('hidden');

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        document.getElementById('resFunc').textContent = data.data.total_funcionarios;
                        document.getElementById('resOcor').textContent = data.data.total_ocorrencias;
                        resultadoDiv.classList.remove('hidden');
                        form.reset();
                        fileName.classList.add('hidden');
                    } else {
                        document.getElementById('resErroMsg').textContent = data.message || data.error || 'Ocorreu um erro desconhecido.';
                        erroDiv.classList.remove('hidden');
                    }
                } catch (error) {
                    console.log(error);

                    document.getElementById('resErroMsg').textContent = 'Erro de conexão ou timeout. Tente novamente.';
                    erroDiv.classList.remove('hidden');
                } finally {
                    // UI Reset state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `<span>Processar Planilha</span>`;
                }
            });
        </script>
    </div>
</body>

</html>