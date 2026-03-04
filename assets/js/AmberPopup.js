"use strict";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import AmberAPI from "/slothsoft@amber/js/AmberAPI";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

function bootstrap() {
    new AmberPopup(window.document);
}

export default class AmberPopup {
    #document;
    #popupNode;

    constructor(document) {
        this.#document = document;
        const rootNode = document.body ? document.body : document.documentElement;

        this.#popupNode = this.#document.createElementNS(NS.XHTML, "div");
        this.#popupNode.setAttribute("class", `amber-popup`);

        rootNode.appendChild(this.#popupNode);
        rootNode.addEventListener("click", this.closePopup.bind(this), false);

        for (const node of this.#document.querySelectorAll("amber-embed[mode~='popup']")) {
            new AmberEmbed(this, node)
        }
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

class AmberEmbed {
    #popup;
    #node;

    #infoset;
    #type;

    constructor(popup, node) {
        this.#popup = popup;
        this.#node = node;

        this.#infoset = this.#node.getAttribute("infoset");
        this.#type = this.#node.getAttribute("type");

        this.#node.setAttribute("aria-role", "button");
        this.#node.setAttribute("tabindex", "0");

        this.#node.addEventListener("click", this.#activatePopup.bind(this), false);
    }

    #activatePopup(eve) {
        const id = parseInt(this.#node.getAttribute("id"));
        if (id) {
            eve.preventDefault();
            eve.stopPropagation();
            this.#popup.openPopup(this.#infoset, this.#type, id);
        }
    }
}

Bootstrap.run(bootstrap);