// Delight Cookie Banner - Admin Live Preview Script
(function () {
	'use strict';

	window.PCDELICOBA_AdminPreview = {
		init: function () {
			const qs = (sel, ctx) => (ctx || document).querySelector(sel);
			const qsa = (sel, ctx) =>
				Array.prototype.slice.call((ctx || document).querySelectorAll(sel));

			const preview = qs('#pcdelicoba-preview');
			if (!preview) return;

			const msgEl = qs('.js-pcdelicoba-message', preview);
			const accBtn = qs('.js-pcdelicoba-accept', preview);
			const rejBtn = qs('.js-pcdelicoba-reject', preview);
			const privEl = qs('.js-pcdelicoba-privacy', preview);

			function setCssVar(name, value) {
				if (!value) return;
				preview.style.setProperty(name, value);
			}

			function updatePosition(pos) {
				preview.classList.remove(
					'pcdelicoba-position-top',
					'pcdelicoba-position-middle',
					'pcdelicoba-position-bottom'
				);
				const valid = ['top', 'middle', 'bottom'];
				const newPos = valid.includes(pos) ? pos : 'bottom';
				preview.classList.add(`pcdelicoba-position-${newPos}`);
			}

			function onChange(e) {
				const el = e.currentTarget;
				const key = el.getAttribute('data-bind');
				if (!key) return;

				const val =
					el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;

				switch (key) {
					case 'bg_color':
						setCssVar('--pcdelicoba-bg-color', val);
						break;
					case 'text_color':
						setCssVar('--pcdelicoba-text-color', val);
						break;
					case 'btn_accept_bg':
						setCssVar('--pcdelicoba-accept-bg', val);
						break;
					case 'btn_accept_text':
						setCssVar('--pcdelicoba-accept-text', val);
						break;
					case 'btn_accept_hover_bg':
						setCssVar('--pcdelicoba-accept-hover-bg', val);
						break;
					case 'btn_accept_hover_text':
						setCssVar('--pcdelicoba-accept-hover-text', val);
						break;
					case 'btn_reject_bg':
						setCssVar('--pcdelicoba-reject-bg', val);
						break;
					case 'btn_reject_text':
						setCssVar('--pcdelicoba-reject-text', val);
						break;
					case 'btn_reject_hover_bg':
						setCssVar('--pcdelicoba-reject-hover-bg', val);
						break;
					case 'btn_reject_hover_text':
						setCssVar('--pcdelicoba-reject-hover-text', val);
						break;
					case 'position':
						updatePosition(val);
						break;
					case 'text_message':
						if (msgEl) msgEl.textContent = val;
						break;
					case 'text_accept':
						if (accBtn) accBtn.textContent = val;
						break;
					case 'text_reject':
						if (rejBtn) rejBtn.textContent = val;
						break;
					default:
						break;
				}
			}

			// Attach listeners
			qsa('.pcdelicoba-bind').forEach((input) => {
				input.addEventListener('input', onChange);
				input.addEventListener('change', onChange);
			});
		},
	};

	document.addEventListener('DOMContentLoaded', function () {
		window.PCDELICOBA_AdminPreview.init();
	});
})();
