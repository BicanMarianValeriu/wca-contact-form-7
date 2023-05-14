/**
 * @package: 	WeCodeArt CF7 Extension
 * @author: 	Bican Marian Valeriu
 * @license:	https://www.wecodeart.com/
 * @version:	1.0.0
 */

import './../scss/index.scss';

export default (function (wecodeart) {
	// const { errorType } = wecodeartCF7;

	wecodeart.routes = {
		...wecodeart.routes,
		wecodeartCf7: {
			complete: () => {
				const forms = document.querySelectorAll('.wpcf7-form');
				[...forms].map(el => el.addEventListener('change', () => el.classList.add('was-validated')));
			}
		}
	};
}).apply(this, [window.wecodeart]);