"use strict";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";

function bootstrap() {
    for (let node of document.querySelectorAll("*.amber-tabs")) {
        node = new TabsView(node);
    }
}

export default class TabsView {

    #rootNode;
    #selectNode;
    #listNode;

    constructor(rootNode) {
        this.#rootNode = rootNode;
        this.#selectNode = this.#rootNode.querySelector(":scope > label > select");
        this.#listNode = this.#rootNode.querySelector(":scope > ul");

        this.#selectNode.addEventListener(
            "change",
            this.changeSectionEvent.bind(this),
            false
        );
        this.#selectNode.disabled = false;

        this.changeSectionEvent();
    }

    changeSectionEvent() {
        const index = parseInt(this.#selectNode.value);
        for (let i = 0; i < this.#listNode.childNodes.length; i++) {
            this.#listNode.childNodes[i].hidden = (i !== index);
        }
    }
}

Bootstrap.run(bootstrap);