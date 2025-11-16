<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name') . ' - Finance Manager')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Vite-built assets (Tailwind CSS + app JS) -- include when manifest/hot exists --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        /* Design tokens for red-black theme (fallbacks) */
        :root{
            --black: #121212;
            --red: #E53935;
            --dark: #1E1E1E;
            --white: #FFFFFF;
            --muted: #9CA3AF;
            --card: #0f1113;
        }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial; background: var(--black); color: var(--white); }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }

        /* Components fallback styles */
        .card { background: var(--card); border-radius: 12px; padding: 1rem; color: var(--white); box-shadow: 0 6px 18px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.03); }
    .btn-primary { display:inline-flex; align-items:center; gap:.5rem; padding:0.5rem 1rem; border-radius:10px; background: var(--red); color:var(--white); font-weight:600; box-shadow: 0 6px 18px rgba(229,57,53,0.12); border: 1px solid rgba(229,57,53,0.12); }
    .btn-primary:hover{ background: #C62828; }

        /* Inputs */
        input, select, textarea { background: #0b0b0b; color: var(--white); border: 1px solid rgba(255,255,255,0.06); padding: .5rem .75rem; border-radius: 8px }

        /* Sidebar */
        .sidebar a { color: #d1d5db; text-decoration: none }

        /* Small helpers */
        .muted { color: var(--muted); }
    </style>
    @yield('extra-styles')
</head>
<body>
    <!-- Sidebar layout: replace top header with left sidebar for authenticated users -->

    <div x-data="{ sidebarOpen: false, isDesktop: window.innerWidth >= 1024, sidebarVisible: true }" @keydown.window.escape="sidebarOpen = false" @resize.window="isDesktop = window.innerWidth >= 1024; if (isDesktop) sidebarOpen = false">
        <div class="min-h-screen flex">
            <!-- Desktop sidebar (can be collapsed with toggle on lg+) -->
            <aside x-cloak :class="sidebarVisible ? 'w-64' : 'w-0'" class="hidden lg:block bg-gradient-to-b from-[#0f1113] to-[#050505] transition-all duration-200 overflow-hidden border-r border-gray-800">
                <div class="p-6">
                    <x-sidebar />
                </div>
            </aside>

            <!-- Mobile sidebar (drawer) -->
            <div x-show="sidebarOpen && !isDesktop" x-cloak class="fixed inset-0 z-50 lg:hidden" x-transition.opacity @click.away="sidebarOpen = false">
                <div @click="sidebarOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur" aria-hidden="true" x-transition.opacity></div>
                <div id="mobile-sidebar" class="absolute left-0 top-0 bottom-0 w-64 bg-[#0f1113] shadow-xl transform" x-show="sidebarOpen && !isDesktop" x-transition:enter="transition transform duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" @keydown.window.escape="sidebarOpen = false" @click.away="sidebarOpen = false">
                    <div class="p-6">
                        <x-sidebar />
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto">
                <!-- Mobile hamburger + Header -->
                <div class="sticky top-0 z-40 bg-[#0f1113] border-b border-gray-800 px-4 py-3 flex items-center gap-3">
                    <!-- Mobile hamburger -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg bg-surface text-white hover:bg-gray-700 transition">
                        <div class="w-6 h-6 flex flex-col justify-between items-center">
                            <span :class="sidebarOpen ? 'transform rotate-45 translate-y-1.5' : ''" class="block h-0.5 w-full bg-white transition-transform duration-200"></span>
                            <span :class="sidebarOpen ? 'opacity-0 scale-0' : ''" class="block h-0.5 w-full bg-white transition-all duration-200"></span>
                            <span :class="sidebarOpen ? 'transform -rotate-45 -translate-y-1.5' : ''" class="block h-0.5 w-full bg-white transition-transform duration-200"></span>
                        </div>
                    </button>

                    <!-- Desktop toggle - collapse / expand sidebar on LG+ -->
                    <button @click="sidebarVisible = !sidebarVisible" class="hidden lg:flex w-10 h-10 items-center justify-center rounded-lg bg-surface text-white hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    </button>

                    <div class="ml-auto text-sm text-muted">{{ config('app.name') }}</div>
                </div>

                <!-- Page content -->
                <div class="p-6 max-w-7xl mx-auto">
                    @yield('content')
                    @auth
                    {{-- AI Chat is now a floating FAB on the bottom-right; see markup after main --}}
                    @endauth
                </div>
            </main>
        </div>
    </div>

    <script>
    function aiChat() {
        return {
            hover: false,
            open: false,
            query: '',
            messages: [],
            loading: false,
            async send() {
                if (!this.query.trim()) return;
                this.messages.push({ role: 'user', content: this.query });
                const userMsg = this.query;
                this.query = '';
                this.loading = true;

                try {
                    const res = await fetch('{{ route("ai.chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: userMsg })
                    });

                    // Check if response is OK
                    if (!res.ok) {
                        let errorMsg = 'Server error (HTTP ' + res.status + ')';
                        const contentType = res.headers.get('content-type');
                        
                        if (contentType && contentType.includes('application/json')) {
                            const errorData = await res.json();
                            errorMsg = errorData.error || errorData.message || errorMsg;
                        } else {
                            const text = await res.text();
                            errorMsg = text.substring(0, 200); // First 200 chars
                        }
                        
                        this.messages.push({ role: 'ai', content: '⚠️ Error: ' + errorMsg });
                    } else {
                        // Try to parse as JSON
                        const text = await res.text();
                        let json;
                        
                        try {
                            json = JSON.parse(text);
                        } catch (parseError) {
                            this.messages.push({ role: 'ai', content: '⚠️ Invalid response format. Response: ' + text.substring(0, 100) });
                            return;
                        }

                        // Handle AI response
                        if (json.error) {
                            this.messages.push({ 
                                role: 'ai', 
                                content: '⚠️ AI Error: ' + (json.message || json.error || 'Unknown error')
                            });
                        } else if (json.raw) {
                            this.messages.push({ role: 'ai', content: json.raw });
                        } else if (json.json) {
                            this.messages.push({ role: 'ai', content: JSON.stringify(json.json, null, 2) });
                        } else {
                            this.messages.push({ role: 'ai', content: 'No response from AI' });
                        }
                    }
                } catch (e) {
                    this.messages.push({ role: 'ai', content: '🔴 Connection Error: ' + e.message });
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                    });
                }
            },
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }
    }
    </script>
    
    @yield('scripts')

    {{-- Floating AI FAB / Chat panel (bottom-right) --}}
    @auth
    <div x-data="aiChat()" x-cloak class="fixed right-6 bottom-6 z-50">
        {{-- Floating button (round) --}}
        <button @click="open = !open" @mouseenter="hover = true" @mouseleave="hover = false" :aria-expanded="open.toString()" aria-label="Open AI chat" class="group relative w-14 h-14 rounded-full bg-red-600 shadow-lg text-white flex items-center justify-center hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h6m-8 8l3-3h6a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v9a2 2 0 002 2z"/></svg>
        </button>

        {{-- Chat panel (floating) --}}
        <div x-show="open" x-transition class="mt-3 w-80 sm:w-96 rounded-xl bg-[#0b0b0b] border border-gray-800 shadow-2xl overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-800 flex items-center justify-between">
                <div class="font-semibold">AI Chat</div>
                <button @click="open = false" class="text-gray-300">✕</button>
            </div>

            <div class="p-3 h-64 overflow-y-auto" x-ref="messages">
                <template x-for="(msg, i) in messages" :key="i">
                    <div class="mb-3" :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                        <div x-text="msg.content" class="inline-block px-3 py-2 rounded-lg" :class="msg.role === 'user' ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-100'"></div>
                    </div>
                </template>
                <div x-show="loading" class="text-center text-gray-400">Sedang berpikir...</div>
            </div>

            <div class="px-3 py-2 bg-transparent border-t border-gray-800">
                <div class="flex gap-2">
                    <input type="text" x-model="query" @keydown.enter="send()" placeholder="Tanya tentang keuangan Anda..." class="flex-1 bg-[#0b0b0b] border border-gray-800 px-3 py-2 rounded-md text-sm text-gray-300 placeholder:text-gray-600 focus:ring-0" />
                    <button @click="send()" class="px-3 py-2 rounded bg-red-600 text-white">Kirim</button>
                </div>
            </div>
        </div>
    </div>
    @endauth
</body>
</html>
