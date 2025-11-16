<aside class="w-64 flex flex-col gap-6 sidebar bg-gradient-to-b from-[#0f1113] to-[#050505]">
    <!-- Logo & Brand -->
    <div class="flex items-center gap-4 px-4 pt-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold text-lg shadow-soft">F</div>
        <div>
            <div class="text-sm font-semibold text-white">{{ config('app.name') }}</div>
            <div class="text-xs text-muted">Finance Manager</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2">
        <ul class="space-y-2">
            @auth
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                   {{ request()->is('dashboard') 
                       ? 'bg-primary text-white shadow-soft font-medium' 
                       : 'text-gray-300 hover:bg-surface hover:text-white' }}">
                    <span class="text-lg">🏠</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transactions.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                   {{ request()->is('transactions*') 
                       ? 'bg-primary text-white shadow-soft font-medium' 
                       : 'text-gray-300 hover:bg-surface hover:text-white' }}">
                    <span class="text-lg">📄</span>
                    <span>Transaksi</span>
                </a>
            </li>
            <li>
                <a href="{{ route('budgets.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                   {{ request()->is('budgets*') 
                       ? 'bg-primary text-white shadow-soft font-medium' 
                       : 'text-gray-300 hover:bg-surface hover:text-white' }}">
                    <span class="text-lg">💼</span>
                    <span>Budget</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 
                   {{ request()->is('reports*') 
                       ? 'bg-primary text-white shadow-soft font-medium' 
                       : 'text-gray-300 hover:bg-surface hover:text-white' }}">
                    <span class="text-lg">📊</span>
                    <span>Laporan</span>
                </a>
            </li>
            @endauth
        </ul>
    </nav>

    <!-- User Section -->
    <div class="mt-auto px-2 pb-4">
        @auth
        <x-card class="mb-4 bg-gradient-to-br from-surface to-[#0a0a0c]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-semibold text-sm shadow-soft">
                    {{ auth()->user()->name[0] ?? 'U' }}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'User' }}</div>
                    <a href="#" class="text-xs text-muted hover:text-gray-200 transition">Profile</a>
                </div>
            </div>
        </x-card>
        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <x-button class="w-full justify-center">Logout</x-button>
        </form>
        @else
        <div class="flex gap-2">
            <a href="{{ route('login') }}" class="flex-1 px-4 py-2 rounded-lg bg-primary text-white text-center text-sm font-medium hover:bg-primary-dark transition shadow-soft">Login</a>
            <a href="{{ route('register') }}" class="flex-1 px-4 py-2 rounded-lg border border-primary text-primary text-center text-sm font-medium hover:bg-primary hover:text-white transition">Register</a>
        </div>
        @endauth
    </div>
</aside>
