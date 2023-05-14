/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
  !*** ./src/js/admin.js ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * @package: 	WeCodeArt CF7 Extension
 * @author: 	Bican Marian Valeriu
 * @license:	https://www.wecodeart.com/
 * @version:	1.0.0
 */
const {
  i18n: {
    __,
    sprintf
  },
  hooks: {
    addFilter
  },
  components: {
    Placeholder,
    ToggleControl,
    SelectControl,
    Dashicon,
    Spinner,
    Tooltip,
    Button
  },
  element: {
    useState,
    useEffect
  }
} = wp;
addFilter('wecodeart.admin.extensions', 'wecodeart/cf7/admin/panel', optionsPanel);

function optionsPanel(panels) {
  return [...panels, {
    name: 'wca-cf7',
    title: __('Contact Form 7', 'wca-cf7'),
    render: props => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Options, props)
  }];
}

const Options = props => {
  const {
    isRequesting,
    wecodeartSettings,
    saveEntityRecord,
    createNotice
  } = props;

  if (isRequesting || !wecodeartSettings) {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Placeholder, {
      icon: (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Spinner, null),
      label: __('Loading', 'wca-cf7'),
      instructions: __('Please wait, loading settings...', 'wca-cf7')
    });
  }

  const [loading, setLoading] = useState(null);

  const apiOptions = (_ref => {
    let {
      cf7_clean_assets,
      cf7_remove_js,
      cf7_remove_css,
      cf7_remove_autop
    } = _ref;
    return {
      cf7_clean_assets,
      cf7_remove_js,
      cf7_remove_css,
      cf7_remove_autop
    };
  })(wecodeartSettings);

  const [formData, setFormData] = useState(apiOptions);

  const handleNotice = () => {
    setLoading(false);
    return createNotice('success', __('Settings saved.', 'wca-cf7'));
  };

  const getHelpText = type => {
    let text = '',
        status = '';

    switch (type) {
      case 'assets':
        status = formData['cf7_clean_assets'] ? __('when the content has a form', 'wca-cf7') : __('on every page', 'wca-cf7');
        text = sprintf(__('Contact Form 7 assets are loaded %s.', 'wca-cf7'), status);
        break;

      case 'JS':
        status = formData['cf7_remove_js'] ? __('removed', 'wca-cf7') : __('loaded', 'wca-cf7');
        text = sprintf(__('Default Contact Form 7 plugin JS will be %s.', 'wca-cf7'), status);
        break;

      case 'CSS':
        status = formData['cf7_remove_css'] ? __('removed', 'wca-cf7') : __('loaded', 'wca-cf7');
        text = sprintf(__('Default Contact Form 7 plugin CSS will be %s.', 'wca-cf7'), status);
        break;

      case 'P':
        status = formData['cf7_remove_autop'] ? __('does not', 'wca-cf7') : __('does', 'wca-cf7');
        text = sprintf(__('Contact Form 7 %s apply the "autop" filter to the form content.', 'wca-cf7'), status);
        break;

      default:
    }

    return text;
  };

  const assetsControl = (formData['cf7_remove_js'] === true && formData['cf7_remove_css'] === true) === false;
  useEffect(() => {
    if (!assetsControl) {
      setFormData({ ...formData,
        'cf7_clean_assets': ''
      });
    }
  }, [assetsControl]);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToggleControl, {
    label: (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, __('Remove JS?', 'wca-cf7'), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Tooltip, {
      text: __('Removing JS will cause the form submission to hard refresh the page!', 'wca-cf7')
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Dashicon, {
      icon: "editor-help",
      style: {
        marginLeft: '1rem',
        color: 'var(--wp-admin-theme-color)'
      }
    }))),
    help: getHelpText('JS'),
    checked: formData['cf7_remove_js'],
    onChange: value => setFormData({ ...formData,
      'cf7_remove_js': value ? value : ''
    })
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToggleControl, {
    label: __('Remove CSS?', 'wca-cf7'),
    help: getHelpText('CSS'),
    checked: formData['cf7_remove_css'],
    onChange: value => setFormData({ ...formData,
      'cf7_remove_css': value ? value : ''
    })
  }), assetsControl && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToggleControl, {
    label: __('Optimize assets loading?', 'wca-cf7'),
    help: getHelpText('assets'),
    checked: formData['cf7_clean_assets'],
    onChange: value => setFormData({ ...formData,
      'cf7_clean_assets': value ? value : ''
    })
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToggleControl, {
    label: __('Remove "autop" filter?', 'wca-cf7'),
    help: getHelpText('P'),
    checked: formData['cf7_remove_autop'],
    onChange: value => setFormData({ ...formData,
      'cf7_remove_autop': value ? value : ''
    })
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("hr", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Button, {
    className: "button",
    isPrimary: true,
    isLarge: true,
    icon: loading && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Spinner, null),
    onClick: () => {
      setLoading(true);
      let value = {};
      Object.keys(formData).map(k => value = { ...value,
        [k]: formData[k] === '' ? 'unset' : formData[k]
      });
      saveEntityRecord('wecodeart', 'settings', value).then(handleNotice);
    },
    disabled: loading
  }, loading ? '' : __('Save', 'wecodeart'))));
};
})();

/******/ })()
;
//# sourceMappingURL=admin.js.map