/**
 * @package: 	WeCodeArt CF7 Extension
 * @author: 	Bican Marian Valeriu
 * @license:	https://www.wecodeart.com/
 * @version:	1.0.0
 */

import './../scss/index.scss';

const TEMPLATES = {
	modal: `<div class="modal modal--cf7 modal--cf7-{{ status }} fade" tabindex="-1" id="modal-cf7-{{ id }}" aria-hidden="true">
		<div class="modal-dialog modal-dialog-{{ position }}">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ title }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ close }}"></button>
				</div>
				<div class="modal-body">{{ content }}</div>
			</div>
		</div>
	</div>`,
	toast: `<div class="toast align-items-center has-{{ color }}-background-color" id="toast-cf7-{{ id }}" role="alert" aria-live="assertive" aria-atomic="true">
		<div class="toast-header">
			<strong class="me-auto">{{ title }}</strong>
			<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ close }}"></button>
		</div>
		<div class="toast-body">{{ content }}</div>
	</div>`
};

export default (function (wecodeart) {
	wecodeart.routes = {
		...wecodeart.routes,
		wecodeartCf7: {
			complete: () => {
				const { Template, plugins } = wecodeart;
				const { labels = {}, feedback: { type = '', position = '' } } = wcaExtCf7Frontend || {};

				const forms = document.querySelectorAll('.wpcf7-form');

				[...forms].map(el => {
					el.addEventListener('wpcf7submit', ({ detail: { apiResponse: {
						status,
						message,
						contact_form_id,
						redirect = false
					} } }) => {
						if (redirect) {
							const { url, blank, delay } = redirect;
							setTimeout((() => {
								if (blank) {
									window.open(url, '_blank');
									return;
								}
								window.location = url;
							}), parseInt(delay));
							return;
						}

						if (type === '') {
							return;
						}

						const JSPlugin = plugins[type];
						const plugin = type.toLowerCase();

						if (!JSPlugin) {
							return;
						}

						let options = {};
						let element = null;

						const feedbackHTML = Template.renderToHTML(TEMPLATES[plugin], {
							status,
							position,
							color: status === 'mail_sent' ? 'success' : 'warning',
							id: contact_form_id,
							content: message,
							title: status === 'mail_sent' ? labels.success : labels.error,
							close: labels.close,
						});

						if (plugin === 'toast') {
							const wrapper = document.createElement('div');
							wrapper.className = `toast-container position-fixed p-3 ${position}`;
							wrapper.append(feedbackHTML);
							element = wrapper;
						} else {
							element = feedbackHTML;
						}

						document.body.appendChild(element);
						const feedbackJS = new JSPlugin(feedbackHTML, options);
						feedbackHTML.addEventListener(`hidden.bs.${plugin}`, () => element.remove());
						feedbackJS.show();
					});

					el.addEventListener('wpcf7mailsent', () => el.classList.remove('was-validated'));
					el.addEventListener('wpcf7mailfailed', () => el.classList.remove('was-validated'));
				});
			}
		}
	};
}).apply(this, [window.wecodeart]);