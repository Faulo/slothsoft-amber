(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[0],{

/***/ "../slothsoft-farah/src-js/DOM/DOMHelper.js":
/*!**************************************************!*\
  !*** ../slothsoft-farah/src-js/DOM/DOMHelper.js ***!
  \**************************************************/
/*! exports provided: DOMHelper */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"DOMHelper\", function() { return DOMHelper; });\n\r\n\r\n\r\nconst parser = new DOMParser();\r\nconst serializer = new XMLSerializer();\r\n\r\nconst DOMHelper = {\r\n\tloadDocument : async function(uri) {\r\n\t\ttry {\r\n\t\t\tif (uri.startsWith(\"farah://\")) {\r\n\t\t\t\turi = \"/getAsset.php/\" + uri.substring(\"farah://\".length);\r\n\t\t\t}\r\n\t\t\tconst response = await fetch(uri);\r\n\t\t\tconst text = await response.text();\r\n\t\t\tconst document = await this.parse(text);\r\n\t\t\tdocument.fileURI = uri;\r\n\t\t\treturn document;\r\n\t\t} catch(e) {\r\n\t\t\tconsole.log(`Could not load XML ressource \"${uri}\"`);\r\n\t\t\tconsole.log(e);\r\n\t\t}\r\n\t},\r\n\tparse : async function(xml) {\r\n\t\tconst [document, namespace] = await Promise.all([this.parser().parseFromString(xml, \"application/xml\"), this.namespace()]);\r\n\t\tif (document.documentElement.namespaceURI === namespace.byName(\"MOZILLA_ERROR\").uri) {\r\n\t\t\tthrow new Error(\"\"+document.documentElement.textContent);\r\n\t\t}\r\n\t\treturn document;\r\n\t},\r\n\tstringify : async function(document) {\r\n\t\treturn await this.serializer().serializeToString(document);\r\n\t},\r\n\ttransformToFragment : async function() {\r\n\t\treturn \"transformation\";\r\n\t},\r\n\tevaluate : async function(query, contextNode = document) {\r\n\t\ttry {\r\n\t\t\tconst ownerDocument = contextNode.nodeType === Node.DOCUMENT_NODE\r\n\t\t\t\t? contextNode\r\n\t\t\t\t: contextNode.ownerDocument;\r\n\t\t\tconst namespace = await this.namespace();\r\n\t\t\tconst result = ownerDocument.evaluate(query, contextNode, namespace.resolve, XPathResult.ANY_TYPE, null);\r\n\t\t\t\r\n\t\t\tswitch (result.resultType) {\r\n\t\t\t\tcase XPathResult.NUMBER_TYPE:\r\n\t\t\t\t\treturn result.numberValue;\r\n\t\t\t\tcase XPathResult.STRING_TYPE:\r\n\t\t\t\t\treturn result.stringValue;\r\n\t\t\t\tcase XPathResult.BOOLEAN_TYPE:\r\n\t\t\t\t\treturn result.booleanValue;\r\n\t\t\t\tdefault:\r\n\t\t\t\t\tlet ret = [];\r\n\t\t\t\t\tlet tmp;\r\n\t\t\t\t\twhile (tmp = result.iterateNext()) {\r\n\t\t\t\t\t\tret.push(tmp);\r\n\t\t\t\t\t}\r\n\t\t\t\t\treturn ret;\r\n\t\t\t}\r\n\t\t} catch(e) {\r\n\t\t\twindow.console.log(\"XPath error!\");\r\n\t\t\twindow.console.log(\"query:%o\", query);\r\n\t\t\twindow.console.log(\"context node:%o\", contextNode);\r\n\t\t\twindow.console.log(\"exception:%o\", e);\r\n\t\t\tthrow e;\r\n\t\t}\r\n\t},\r\n\tparser : function() {\r\n\t\tif (!this._parser) {\r\n\t\t\tthis._parser = new DOMParser();\r\n\t\t}\r\n\t\treturn this._parser;\r\n\t},\r\n\tserializer : function() {\r\n\t\tif (!this._serializer) {\r\n\t\t\tthis._serializer = new XMLSerializer();\r\n\t\t}\r\n\t\treturn this._serializer;\r\n\t},\r\n\tnamespace : async function() {\r\n\t\tif (!this._namespace) {\r\n\t\t\tlet module = await Promise.resolve(/*! import() */).then(__webpack_require__.bind(null, /*! ./Namespace */ \"../slothsoft-farah/src-js/DOM/Namespace.js\"));\r\n\t\t\tthis._namespace = module.Namespace;\r\n\t\t}\r\n\t\treturn this._namespace;\r\n\t},\r\n}\n\n//# sourceURL=webpack:///../slothsoft-farah/src-js/DOM/DOMHelper.js?");

/***/ }),

/***/ "../slothsoft-farah/src-js/DOM/Namespace.js":
/*!**************************************************!*\
  !*** ../slothsoft-farah/src-js/DOM/Namespace.js ***!
  \**************************************************/
/*! exports provided: Namespace */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"Namespace\", function() { return Namespace; });\n\r\n\r\nconst list = [];\r\n\r\nclass Namespace {\r\n\tstatic register(name, prefix, uri) {\r\n\t\tlist.push({name, prefix, uri});\r\n\t}\r\n\tstatic byName(name) {\r\n\t\tfor (let i = 0; i < list.length; i++) {\r\n\t\t\tif (list[i].name == name) {\r\n\t\t\t\treturn list[i];\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tstatic byPrefix(prefix) {\r\n\t\tfor (let i = 0; i < list.length; i++) {\r\n\t\t\tif (list[i].prefix == prefix) {\r\n\t\t\t\treturn list[i];\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tstatic byUri(uri) {\r\n\t\tfor (let i = 0; i < list.length; i++) {\r\n\t\t\tif (list[i].uri == uri) {\r\n\t\t\t\treturn list[i];\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tstatic resolve(prefix) {\r\n\t\treturn Namespace.byPrefix(prefix).uri;\r\n\t}\r\n\tstatic prefix(uri) {\r\n\t\treturn Namespace.byUri(uri).prefix;\r\n\t}\r\n}\r\n\r\nNamespace.register(\"XML\", \"xml\", \"http://www.w3.org/XML/1998/namespace\");\r\nNamespace.register(\"HTML\", \"html\", \"http://www.w3.org/1999/xhtml\");\r\nNamespace.register(\"MOZILLA_ERROR\", \"me\", \"http://www.mozilla.org/newlayout/xml/parsererror.xml\");\r\nNamespace.register(\"MOZILLA_XUL\", \"mx\", \"http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul\");\r\n\r\nNamespace.register(\"AMBER_AMBERDATA\", \"saa\", \"http://schema.slothsoft.net/amber/amberdata\");\r\nNamespace.register(\"FARAH_MODULE\", \"sfm\", \"http://schema.slothsoft.net/farah/module\");\n\n//# sourceURL=webpack:///../slothsoft-farah/src-js/DOM/Namespace.js?");

/***/ }),

/***/ "../slothsoft-farah/src-js/DOM/index.js":
/*!**********************************************!*\
  !*** ../slothsoft-farah/src-js/DOM/index.js ***!
  \**********************************************/
/*! exports provided: DOMHelper, Namespace */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _DOMHelper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DOMHelper */ \"../slothsoft-farah/src-js/DOM/DOMHelper.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"DOMHelper\", function() { return _DOMHelper__WEBPACK_IMPORTED_MODULE_0__[\"DOMHelper\"]; });\n\n/* harmony import */ var _Namespace__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Namespace */ \"../slothsoft-farah/src-js/DOM/Namespace.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Namespace\", function() { return _Namespace__WEBPACK_IMPORTED_MODULE_1__[\"Namespace\"]; });\n\n\r\n\r\n\r\n\n\n//# sourceURL=webpack:///../slothsoft-farah/src-js/DOM/index.js?");

/***/ }),

/***/ "../slothsoft-farah/src-js/Module/index.js":
/*!*************************************************!*\
  !*** ../slothsoft-farah/src-js/Module/index.js ***!
  \*************************************************/
/*! exports provided: resolveToDocument */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"resolveToDocument\", function() { return resolveToDocument; });\n\r\nasync function resolveToDocument(farahUrl) {\r\n\tlet DOM = await Promise.resolve(/*! import() */).then(__webpack_require__.bind(null, /*! ../DOM/index */ \"../slothsoft-farah/src-js/DOM/index.js\"));\r\n\treturn await DOM.DOMHelper.loadDocument(\"\"+farahUrl);\r\n}\n\n//# sourceURL=webpack:///../slothsoft-farah/src-js/Module/index.js?");

/***/ }),

/***/ "../slothsoft-farah/src-js/index.js":
/*!******************************************!*\
  !*** ../slothsoft-farah/src-js/index.js ***!
  \******************************************/
/*! exports provided: DOM, Module */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _DOM_index__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DOM/index */ \"../slothsoft-farah/src-js/DOM/index.js\");\n/* harmony reexport (module object) */ __webpack_require__.d(__webpack_exports__, \"DOM\", function() { return _DOM_index__WEBPACK_IMPORTED_MODULE_0__; });\n/* harmony import */ var _Module_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Module/index */ \"../slothsoft-farah/src-js/Module/index.js\");\n/* harmony reexport (module object) */ __webpack_require__.d(__webpack_exports__, \"Module\", function() { return _Module_index__WEBPACK_IMPORTED_MODULE_1__; });\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\n\n//# sourceURL=webpack:///../slothsoft-farah/src-js/index.js?");

/***/ })

}]);