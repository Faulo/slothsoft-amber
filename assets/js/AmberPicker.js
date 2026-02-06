"use strict";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import AmberAPI from "/slothsoft@amber/js/AmberAPI";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

function bootstrap() {
    window.AmberPicker = new AmberPicker(window.document);
}

export default class AmberPicker {
    #document;
    #rootNode;
    #popupNode;
    #embeds = [];

    constructor(document) {
        this.#document = document;
        this.#rootNode = document.body ? document.body : document.documentElement;

        this.#popupNode = this.#document.createElementNS(NS.XHTML, "div");
        this.#popupNode.setAttribute("class", `amber-popup`);
        this.#rootNode.appendChild(this.#popupNode);
        this.#rootNode.addEventListener("click", this.closePopup.bind(this), false);

        for (let node of this.#document.querySelectorAll("amber-embed[mode]")) {
            this.#embeds.push(new AmberEmbed(this, node));
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
    #picker;
    #node;
    #type;
    #modes = {
        "popup": false,
        "picker": false
    };

    constructor(picker, node) {
        this.#picker = picker;
        this.#node = node;

        this.#type = this.#node.getAttribute("type");

        for (let mode of this.#node.getAttribute("mode").split(" ")) {
            this.#modes[mode] = true;
        }

        this.#node.setAttribute("aria-role", "button");
        this.#node.setAttribute("tabindex", "0");

        if (this.#modes.popup) {
            this.#node.addEventListener("click", this.#activatePopup.bind(this), false);
        }

        if (this.#modes.picker) {
            this.#node.addEventListener("contextmenu", this.#activatePicker.bind(this), false);
        }
    }

    #activatePopup(eve) {
        const id = parseInt(this.#node.getAttribute("id"));
        if (id) {
            eve.preventDefault();
            eve.stopPropagation();
            this.#picker.openPopup(this.#node.getAttribute("infoset"), this.#type, id);
        }
    }

    #activatePicker(eve) {
        eve.preventDefault();
        eve.stopPropagation();
        console.log("activating picker");
    }
}

Bootstrap.run(bootstrap);