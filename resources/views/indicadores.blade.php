<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores / Rankings - Sistema de Ocorrências</title>
    <meta name="description" content="Ranking de funcionários por tipo de ocorrência no Sistema de Ocorrências.">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.8); }
        ::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.5); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(99, 102, 241, 0.8); }
        .tipo-card { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; position: relative; overflow: hidden; }
        .tipo-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), transparent); opacity: 0; transition: opacity 0.3s ease; }
        .tipo-card:hover::before, .tipo-card.active::before { opacity: 1; }
        .tipo-card:hover { border-color: rgba(245, 158, 11, 0.4); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3); }
        .tipo-card.active { border-color: rgba(245, 158, 11, 0.6); box-shadow: 0 0 20px rgba(245, 158, 11, 0.15), 0 8px 25px rgba(0, 0, 0, 0.3); }
        .tipo-badge { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; flex-shrink: 0; }
        .ranking-row { transition: all 0.2s ease; border-bottom: 1px solid rgba(255, 255, 255, 0.04); }
        .ranking-row:hover { background: rgba(255, 255, 255, 0.03); }
        .ranking-row:last-child { border-bottom: none; }
        .progress-bar { height: 10px; border-radius: 5px; background: linear-gradient(90deg, #f59e0b, #d97706); transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 8px rgba(245, 158, 11, 0.3); }
        .pos-1 { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
        .pos-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; }
        .pos-3 { background: linear-gradient(135deg, #b45309, #92400e); color: #fff; }
        .pos-default { background: rgba(51, 65, 85, 0.6); color: rgba(148, 163, 184, 1); }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .skeleton { background: linear-gradient(90deg, rgba(51, 65, 85, 0.4) 25%, rgba(71, 85, 105, 0.4) 50%, rgba(51, 65, 85, 0.4) 75%); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; border-radius: 6px; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Carrossel */
        .carousel-wrapper { position: relative; }
        .carousel-track { display: flex; gap: 0.75rem; overflow: hidden; scroll-behavior: smooth; }
        .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); z-index: 10; width: 36px; height: 36px; border-radius: 50%; background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(255, 255, 255, 0.15); color: #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; backdrop-filter: blur(8px); }
        .carousel-btn:hover { background: rgba(99, 102, 241, 0.6); border-color: rgba(99, 102, 241, 0.8); color: #fff; }
        .carousel-btn:disabled { opacity: 0.3; cursor: not-allowed; }
        .carousel-btn.left { left: -12px; }
        .carousel-btn.right { right: -12px; }

        /* Paginação */
        .pagination-btn { min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 0.8rem; font-weight: 600; transition: all 0.2s ease; cursor: pointer; border: 1px solid rgba(255, 255, 255, 0.08); background: rgba(15, 23, 42, 0.6); color: #94a3b8; }
        .pagination-btn:hover:not(:disabled):not(.active) { background: rgba(99, 102, 241, 0.2); border-color: rgba(99, 102, 241, 0.4); color: #e2e8f0; }
        .pagination-btn.active { background: rgba(99, 102, 241, 0.6); border-color: rgba(99, 102, 241, 0.8); color: #fff; }
        .pagination-btn:disabled { opacity: 0.3; cursor: not-allowed; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen text-slate-200 flex flex-col p-6">

    <!-- Navbar -->
    <nav class="glass rounded-xl px-6 py-4 mb-6 flex justify-between items-center max-w-7xl w-full mx-auto" id="main-navbar">
        <div class="flex items-center gap-3">
            <div class="bg-amber-500/20 w-10 h-10 rounded-full flex items-center justify-center border border-amber-400/30">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white tracking-wide">Indicadores / Rankings</h1>
        </div>
        <div class="flex gap-4 items-center">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('upload.index') }}" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Upload Planilha</a>
            @endif
            <a href="{{ route('consulta.index') }}" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Consultar</a>
            <a href="{{ route('indicadores.index') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600/80 hover:bg-indigo-500/80 rounded-lg transition-colors border border-indigo-500/50 shadow-[0_0_15px_rgba(99,102,241,0.2)]">Indicadores</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Usuários</a>
                <a href="{{ route('logs.index') }}" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-colors border border-slate-700/50">Logs</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" class="ml-2">
                @csrf
                <button type="submit" class="px-3 py-2 text-sm font-medium text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors border border-red-500/20">Sair</button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl w-full mx-auto flex flex-col gap-6 flex-1">

        <!-- Seção: Tipos de Ocorrência com Carrossel -->
        <div class="glass rounded-2xl shadow-xl p-6" id="tipos-section">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Tipos de Ocorrência
                </h2>
                <p class="text-xs text-slate-400 mt-1">Selecione um tipo para visualizar o ranking de funcionários</p>
            </div>

            <div class="carousel-wrapper">
                <button class="carousel-btn left" id="carousel-prev" onclick="carouselScroll(-1)" aria-label="Anterior">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div class="carousel-track" id="tipos-container">
                    @foreach($tipos as $index => $tipo)
                        <button class="tipo-card rounded-xl px-5 py-3.5 flex items-center gap-3 min-w-[180px] flex-shrink-0"
                            data-tipo="{{ $tipo->tipo_ocorrencia }}" id="tipo-btn-{{ $index }}"
                            onclick="selecionarTipo(this, '{{ addslashes($tipo->tipo_ocorrencia) }}')">
                            <span class="tipo-badge pos-default">{{ $index + 1 }}</span>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-white leading-tight">{{ $tipo->tipo_ocorrencia }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ number_format($tipo->total, 0, ',', '.') }} ocorrência{{ $tipo->total != 1 ? 's' : '' }}</div>
                            </div>
                        </button>
                    @endforeach
                    @if($tipos->isEmpty())
                        <div class="text-sm text-slate-500 py-4">Nenhum tipo de ocorrência cadastrado.</div>
                    @endif
                </div>
                <button class="carousel-btn right" id="carousel-next" onclick="carouselScroll(1)" aria-label="Próximo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Seção: Ranking -->
        <div class="glass rounded-2xl shadow-xl overflow-hidden flex-1 flex flex-col" id="ranking-section">
            <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2" id="ranking-title">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span id="ranking-label">Ranking</span>
                </h2>
                <span class="text-xs px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 hidden" id="ranking-count"></span>
            </div>

            <!-- Cabeçalho da Tabela -->
            <div class="bg-slate-800/60 border-b border-slate-700/50">
                <div class="grid grid-cols-12 gap-4 px-6 py-3 text-xs text-slate-400 uppercase tracking-wider font-semibold">
                    <div class="col-span-1 text-center">#</div>
                    <div class="col-span-4">Funcionário</div>
                    <div class="col-span-4">Centro de Custo</div>
                    <div class="col-span-3 text-right">Qtd. Ocorrências</div>
                </div>
            </div>

            <!-- Corpo do Ranking -->
            <div class="flex-1 overflow-y-auto" id="ranking-body">
                <div class="flex flex-col items-center justify-center py-16 text-slate-500" id="ranking-placeholder">
                    <svg class="w-16 h-16 mb-4 text-slate-600/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-sm">Selecione um tipo de ocorrência acima para visualizar o ranking.</p>
                </div>
                <div class="hidden" id="ranking-loading">
                    <div class="px-6 py-3 space-y-4">
                        <div class="skeleton h-12 w-full"></div><div class="skeleton h-12 w-full"></div>
                        <div class="skeleton h-12 w-full"></div><div class="skeleton h-12 w-full"></div>
                        <div class="skeleton h-12 w-full"></div>
                    </div>
                </div>
                <div class="hidden" id="ranking-data"></div>
                <div class="hidden flex-col items-center justify-center py-16 text-slate-500" id="ranking-empty">
                    <svg class="w-12 h-12 mb-3 text-slate-600/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm">Nenhum funcionário encontrado para este tipo de ocorrência.</p>
                </div>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-3 border-t border-slate-700/50 bg-slate-800/20 hidden" id="ranking-pagination">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400" id="pagination-info"></span>
                    <div class="flex items-center gap-2" id="pagination-buttons"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let tipoSelecionado = null;
        let paginaAtual = 1;

        // === CARROSSEL ===
        const track = document.getElementById('tipos-container');
        function carouselScroll(direction) {
            const cardWidth = track.querySelector('.tipo-card')?.offsetWidth || 200;
            const scrollAmount = (cardWidth + 12) * 2; // 2 cards por clique
            track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
            setTimeout(updateCarouselButtons, 350);
        }
        function updateCarouselButtons() {
            const prev = document.getElementById('carousel-prev');
            const next = document.getElementById('carousel-next');
            if (!track || !prev || !next) return;
            prev.disabled = track.scrollLeft <= 5;
            next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 5;
        }
        track?.addEventListener('scroll', updateCarouselButtons);
        window.addEventListener('resize', updateCarouselButtons);
        document.addEventListener('DOMContentLoaded', () => setTimeout(updateCarouselButtons, 100));

        // === SELEÇÃO DE TIPO ===
        function selecionarTipo(el, tipo) {
            document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            tipoSelecionado = tipo;
            paginaAtual = 1;
            carregarRanking(tipo, 1);
        }

        // === CARREGAR RANKING ===
        async function carregarRanking(tipo, page) {
            const placeholder = document.getElementById('ranking-placeholder');
            const loading = document.getElementById('ranking-loading');
            const dataContainer = document.getElementById('ranking-data');
            const emptyContainer = document.getElementById('ranking-empty');
            const rankingLabel = document.getElementById('ranking-label');
            const rankingCount = document.getElementById('ranking-count');
            const paginationDiv = document.getElementById('ranking-pagination');

            placeholder.classList.add('hidden');
            dataContainer.classList.add('hidden');
            emptyContainer.classList.add('hidden');
            emptyContainer.style.display = 'none';
            paginationDiv.classList.add('hidden');
            loading.classList.remove('hidden');

            rankingLabel.textContent = 'Ranking - ' + tipo;

            try {
                const res = await fetch(`/indicadores/ranking?tipo=${encodeURIComponent(tipo)}&page=${page}`);
                if (!res.ok) throw new Error('Erro ao carregar ranking');
                const data = await res.json();

                loading.classList.add('hidden');

                if (data.ranking.length === 0 && data.total_funcionarios === 0) {
                    emptyContainer.classList.remove('hidden');
                    emptyContainer.style.display = 'flex';
                    rankingCount.classList.add('hidden');
                    return;
                }

                rankingCount.textContent = data.total_funcionarios + ' funcionário(s) no ranking';
                rankingCount.classList.remove('hidden');

                let html = '';
                data.ranking.forEach((item, idx) => {
                    const percent = Math.max(8, (item.total_ocorrencias / data.max) * 100);
                    const posClass = item.posicao === 1 ? 'pos-1' :
                                     item.posicao === 2 ? 'pos-2' :
                                     item.posicao === 3 ? 'pos-3' : 'pos-default';
                    html += `
                        <div class="ranking-row grid grid-cols-12 gap-4 px-6 py-3 items-center animate-fade-in" style="animation-delay: ${idx * 40}ms">
                            <div class="col-span-1 flex justify-center">
                                <span class="tipo-badge ${posClass}">${item.posicao}</span>
                            </div>
                            <div class="col-span-4">
                                <div class="font-semibold text-white text-sm leading-tight">${item.nome}</div>
                                <div class="text-xs text-slate-400 mt-0.5">Filial: ${item.filial}</div>
                            </div>
                            <div class="col-span-4 text-sm text-slate-300 truncate" title="${item.centro_custo}">
                                ${item.centro_custo}
                            </div>
                            <div class="col-span-3 flex items-center gap-3 justify-end">
                                <div class="flex-1 bg-slate-700/30 rounded-full h-[10px] overflow-hidden max-w-[150px]">
                                    <div class="progress-bar" style="width: 0%" data-width="${percent}%"></div>
                                </div>
                                <span class="text-sm font-bold text-white w-8 text-right">${item.total_ocorrencias}</span>
                            </div>
                        </div>`;
                });

                dataContainer.innerHTML = html;
                dataContainer.classList.remove('hidden');

                requestAnimationFrame(() => {
                    setTimeout(() => {
                        dataContainer.querySelectorAll('.progress-bar').forEach(bar => {
                            bar.style.width = bar.dataset.width;
                        });
                    }, 100);
                });

                // Renderizar paginação
                paginaAtual = data.page;
                renderizarPaginacao(data.page, data.total_pages, data.total_funcionarios, data.per_page);

            } catch (err) {
                console.error('Erro ao carregar ranking:', err);
                loading.classList.add('hidden');
                emptyContainer.classList.remove('hidden');
                emptyContainer.style.display = 'flex';
                rankingCount.classList.add('hidden');
            }
        }

        // === PAGINAÇÃO ===
        function renderizarPaginacao(page, totalPages, totalFunc, perPage) {
            const paginationDiv = document.getElementById('ranking-pagination');
            const infoEl = document.getElementById('pagination-info');
            const btnsEl = document.getElementById('pagination-buttons');

            if (totalPages <= 1) {
                paginationDiv.classList.add('hidden');
                return;
            }

            paginationDiv.classList.remove('hidden');

            const from = (page - 1) * perPage + 1;
            const to = Math.min(page * perPage, totalFunc);
            infoEl.textContent = `Exibindo ${from}-${to} de ${totalFunc} funcionário(s)`;

            let html = '';
            // Botão Anterior
            html += `<button class="pagination-btn" onclick="irParaPagina(${page - 1})" ${page <= 1 ? 'disabled' : ''}>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>`;

            // Páginas
            const maxVisible = 5;
            let start = Math.max(1, page - Math.floor(maxVisible / 2));
            let end = Math.min(totalPages, start + maxVisible - 1);
            if (end - start < maxVisible - 1) start = Math.max(1, end - maxVisible + 1);

            if (start > 1) {
                html += `<button class="pagination-btn" onclick="irParaPagina(1)">1</button>`;
                if (start > 2) html += `<span class="text-slate-500 text-xs px-1">...</span>`;
            }
            for (let i = start; i <= end; i++) {
                html += `<button class="pagination-btn ${i === page ? 'active' : ''}" onclick="irParaPagina(${i})">${i}</button>`;
            }
            if (end < totalPages) {
                if (end < totalPages - 1) html += `<span class="text-slate-500 text-xs px-1">...</span>`;
                html += `<button class="pagination-btn" onclick="irParaPagina(${totalPages})">${totalPages}</button>`;
            }

            // Botão Próximo
            html += `<button class="pagination-btn" onclick="irParaPagina(${page + 1})" ${page >= totalPages ? 'disabled' : ''}>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>`;

            btnsEl.innerHTML = html;
        }

        function irParaPagina(page) {
            if (!tipoSelecionado || page < 1) return;
            carregarRanking(tipoSelecionado, page);
            // Scroll suave para o topo do ranking
            document.getElementById('ranking-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Auto-selecionar o primeiro tipo ao carregar
        document.addEventListener('DOMContentLoaded', function () {
            const firstCard = document.querySelector('.tipo-card');
            if (firstCard) firstCard.click();
        });
    </script>
</body>
</html>