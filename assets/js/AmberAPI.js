"use strict";

import DOM from "/slothsoft@farah/js/DOM";

class AmberAPI {
    static #urls = {
        "amberdata": "/slothsoft@amber/api/amberdata",
        "viewer": "/slothsoft@amber/api/viewer",
    };

    #args = ["repository", "game", "version"];

    #getUrl(type, infosetId) {
        let url = AmberAPI.#urls[type] + "?infosetId=" + infosetId;

        for (const key of this.#args) {
            const node = document.querySelector(`input[name="${key}"]`);
            if (node) {
                const value = node.value;
                url += `&${key}=${value}`;
            }
        }

        return url;
    }

    #documents = {};

    #getDocument(type, infosetId) {
        const url = this.#getUrl(type, infosetId);

        if (this.#documents[url]) {
            return Promise.resolve(this.#documents[url]);
        }

        return DOM.loadDocumentAsync(url).then(d => this.#documents[url] = d);
    }

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
        return this.#getDocument("amberdata", infosetId);
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
        return this.#getDocument("viewer", infosetId);
    }
}

export default new AmberAPI();