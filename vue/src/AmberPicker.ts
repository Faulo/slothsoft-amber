"use strict";

import { createApp } from "vue";
import ContextMenu from "./components/ContextMenu";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

function instantiate(node: Element) {
    return [
        { type: "toggle", id: "identified", label: "Ist identifiziert" },
    ];
}

function bind(node: Element) {
    return [
        { label: "Öffnen", icon: "📂", shortcut: "Enter", onClick: () => console.log("open") },
        { label: "Umbenennen", icon: "✏️", onClick: () => console.log("rename") },
        { label: "Löschen", icon: "🗑️", shortcut: "Del", onClick: () => console.log("delete") },
        { label: "Deaktiviert", icon: "🚫", disabled: true },
    ];
}

function bootstrap(): void {
    const cm = new ContextMenu(instantiate, bind);

    const root = document.createElementNS(NS.HTML, "div");
    document.documentElement.appendChild(root);
    createApp(cm.component).mount(root);

    document.querySelectorAll("amber-embed[mode~='picker']").forEach((node: Element) => cm.registerMenu(node));
}

Bootstrap.run(bootstrap);