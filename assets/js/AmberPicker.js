"use strict";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import DOM from "/slothsoft@farah/js/DOM";
import XSLT from "/slothsoft@farah/js/XSLT";

const viewerUrl = "/slothsoft@amber/api/viewer";

function bootstrap() {
    window.AmberPicker = new AmberPicker(viewerUrl);
}

export default class AmberPicker {
    constructor(viewerUrl) {
    }

    closePopup() {

    }

    openPopup(eve) {
        console.info(eve);
    }
}

Bootstrap.run(bootstrap);