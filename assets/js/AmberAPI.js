"use strict";

import DOM from "/slothsoft@farah/js/DOM";

class AmberAPI {
    static #urls = {
        "amberdata": "/slothsoft@amber/api/amberdata",
        "viewer": "/slothsoft@amber/api/viewer",
    };

    #documents = {};

    getAmberdataItems(itemIds) {
        return this.getAmberdataDocument("lib.items")
            .then(document => itemIds.map(itemId => document.querySelector(`item[id="${itemId}"]`)));
    }

    getAmberdataItem(itemId) {
        return this.getAmberdataDocument("lib.items")
            .then(document => document.querySelector(`item[id="${itemId}"]`));
    }

    getAmberdataElement(infosetId, type, id) {
        return this
            .getAmberdataDocument(infosetId)
            .then(document => {
                const amberdataNode = document.querySelector(`${type}[id="${id}"]`);
                if (amberdataNode) {
                    return amberdataNode;
                }

                return Promise.reject(`Failed to find amberdata of type ${type} with ID ${id} in infoset ${infosetId}`);
            });
    }

    getAmberdataDocument(infosetId) {
        const url = AmberAPI.#urls.amberdata + "?infosetId=" + infosetId;

        if (this.#documents[url]) {
            return Promise.resolve(this.#documents[url]);
        }

        return DOM.loadDocumentAsync(url).then(d => this.#documents[url] = d);
    }

    getViewElement(infosetId, type, id) {
        return this
            .getViewDocument(infosetId)
            .then(document => {
                const viewNode = document.querySelector(`*[data-${type}-id="${id}"]`);
                if (viewNode) {
                    return viewNode;
                }

                return Promise.reject(`Failed to find view of type ${type} with ID ${id} in infoset ${infosetId}`);
            });
    }

    getViewDocument(infosetId) {
        const url = AmberAPI.#urls.viewer + "?infosetId=" + infosetId;

        if (this.#documents[url]) {
            return Promise.resolve(this.#documents[url]);
        }

        return DOM.loadDocumentAsync(url).then(d => this.#documents[url] = d);
    }
}

export default new AmberAPI();