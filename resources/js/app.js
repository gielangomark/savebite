import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	if (document.getElementById('global-live-clock')) return;

	const fallbackClock = document.createElement('div');
	fallbackClock.id = 'global-live-clock';
	fallbackClock.setAttribute('aria-label', 'Waktu saat ini');
	fallbackClock.style.cssText = 'position:fixed;right:1rem;bottom:4rem;z-index:100;padding:.5rem .75rem;border:1px solid rgba(15,23,42,.1);border-radius:.5rem;background:rgba(255,255,255,.9);box-shadow:0 4px 6px -1px rgba(15,23,42,.1);font-size:.75rem;font-weight:700;font-variant-numeric:tabular-nums;color:#334155;backdrop-filter:blur(4px)';
	fallbackClock.appendChild(document.createElement('span'));
	document.body.appendChild(fallbackClock);

	const updateClock = () => {
		fallbackClock.querySelector('span').textContent = new Intl.DateTimeFormat('id-ID', {
			hour: '2-digit',
			minute: '2-digit',
			second: '2-digit'
		}).format(new Date());
	};

	updateClock();
	setInterval(updateClock, 1000);
});
