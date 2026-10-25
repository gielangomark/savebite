<div id="global-live-clock" class="{{ ($flow ?? false) ? 'mx-4 mt-8 mb-4 flex justify-end sm:mx-6' : 'fixed bottom-16 right-4 z-[100]' }}" aria-label="Waktu saat ini">
    <span class="rounded-lg border border-slate-900/10 bg-white/90 px-3 py-2 text-xs font-bold tabular-nums text-slate-700 shadow-md backdrop-blur-sm"></span>
</div>

<script>
    (() => {
        const clock = document.getElementById('global-live-clock');

        if (!clock) return;

        const updateClock = () => {
            clock.querySelector('span').textContent = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }).format(new Date());
        };

        updateClock();
        setInterval(updateClock, 1000);
    })();
</script>
