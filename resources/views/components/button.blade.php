<button {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white font-semibold shadow-soft hover:bg-primary-dark transition']) }}>
    {{ $slot }}
</button>
