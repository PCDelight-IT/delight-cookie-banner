// Delight Cookie Banner - Admin Live Preview Script
(function () {
	'use strict';

	window.DCB_AdminPreview = {
		init: function () {
			const qs = (sel, ctx) => (ctx || document).querySelector(sel);
			const qsa = (sel, ctx) =>
				Array.prototype.slice.call((ctx || document).querySelectorAll(sel));

			const preview = qs('#dcb-preview');
			if (!preview) return;

			const msgEl = qs('.js-dcb-message', preview);
			const accBtn = qs('.js-dcb-accept', preview);
			const rejBtn = qs('.js-dcb-reject', preview);
			const privEl = qs('.js-dcb-privacy', preview);

			function setCssVar(name, value) {
				if (!value) return;
				preview.style.setProperty(name, value);
			}

			function updatePosition(pos) {
				preview.classList.remove(
					'dcb-position-top',
					'dcb-position-middle',
					'dcb-position-bottom'
				);
				const valid = ['top', 'middle', 'bottom'];
				const newPos = valid.includes(pos) ? pos : 'bottom';
				preview.classList.add(`dcb-position-${newPos}`);
			}

			function onChange(e) {
				const el = e.currentTarget;
				const key = el.getAttribute('data-bind');
				if (!key) return;

				const val =
					el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;

				switch (key) {
					case 'bg_color':
						setCssVar('--dcb-bg-color', val);
						break;
					case 'text_color':
						setCssVar('--dcb-text-color', val);
						break;
					case 'btn_accept_bg':
						setCssVar('--dcb-accept-bg', val);
						break;
					case 'btn_accept_text':
						setCssVar('--dcb-accept-text', val);
						break;
					case 'btn_accept_hover_bg':
						setCssVar('--dcb-accept-hover-bg', val);
						break;
					case 'btn_accept_hover_text':
						setCssVar('--dcb-accept-hover-text', val);
						break;
					case 'btn_reject_bg':
						setCssVar('--dcb-reject-bg', val);
						break;
					case 'btn_reject_text':
						setCssVar('--dcb-reject-text', val);
						break;
					case 'btn_reject_hover_bg':
						setCssVar('--dcb-reject-hover-bg', val);
						break;
					case 'btn_reject_hover_text':
						setCssVar('--dcb-reject-hover-text', val);
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
			qsa('.dcb-bind').forEach((input) => {
				input.addEventListener('input', onChange);
				input.addEventListener('change', onChange);
			});
		},
	};

	document.addEventListener('DOMContentLoaded', function () {
		window.DCB_AdminPreview.init();
	});
})();
