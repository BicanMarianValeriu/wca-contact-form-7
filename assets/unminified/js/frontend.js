/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/scss/index.scss":
/*!*****************************!*\
  !*** ./src/scss/index.scss ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*************************!*\
  !*** ./src/js/index.js ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _scss_index_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../scss/index.scss */ "./src/scss/index.scss");
/**
 * @package: 	WeCodeArt CF7 Extension
 * @author: 	Bican Marian Valeriu
 * @license:	https://www.wecodeart.com/
 * @version:	1.0.0
 */

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
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((function (wecodeart) {
  wecodeart.routes = { ...wecodeart.routes,
    wecodeartCf7: {
      complete: () => {
        const {
          Template,
          plugins
        } = wecodeart;
        const {
          labels = {},
          feedback: {
            type = '',
            position = ''
          }
        } = wcaExtCf7Frontend || {};
        const forms = document.querySelectorAll('.wpcf7-form');
        [...forms].map(el => {
          el.addEventListener('wpcf7submit', _ref => {
            let {
              detail: {
                apiResponse: {
                  status,
                  message,
                  contact_form_id,
                  redirect = false
                }
              }
            } = _ref;

            if (redirect) {
              const {
                url,
                blank,
                delay
              } = redirect;
              setTimeout(() => {
                if (blank) {
                  window.open(url, '_blank');
                  return;
                }

                window.location = url;
              }, parseInt(delay));
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
              close: labels.close
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
}).apply(undefined, [window.wecodeart]));
})();

/******/ })()
;
//# sourceMappingURL=frontend.js.map