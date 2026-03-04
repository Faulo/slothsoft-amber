"use strict";

import Vue from "https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js";
import ContextMenu from "https://cdn.jsdelivr.net/npm/@imengyu/vue3-context-menu@1/lib/vue3-context-menu.es.js";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import AmberAPI from "/slothsoft@amber/js/AmberAPI";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

function bootstrap() {
    const app = Vue.createApp();
    app.use(ContextMenu);
    app.mount(window.document);
    alert(app);
}

export default class AmberPicker {
    #document;
    #contextMenu;
    #popupNode;
    #embeds = [];

    constructor(document) {
        this.#contextMenu = createContextMenu();
        this.#contextMenu.open();
    }

    closePopup() {
        while (this.#popupNode.hasChildNodes()) {
            this.#popupNode.removeChild(this.#popupNode.lastChild);
        }

        this.#popupNode.setAttribute("class", `amber-popup`);
    }

    openPopup(infosetId, type, id) {
        AmberAPI
            .getViewElement(infosetId, type, id)
            .then(infosetNode => {
                this.closePopup();
                this.#popupNode.setAttribute("class", `amber-popup amber-popup--visible amber-poup--${type}`);
                this.#popupNode.appendChild(this.#document.importNode(infosetNode, true));
            });
    }
}

Bootstrap.run(bootstrap);