"use strict";

import { createApp } from "vue";
import { createContextMenu } from "./components/ContextMenu.js";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

function bootstrap(): void {
    const cm = createContextMenu();
    const component = cm.ContextMenu;

    const root = document.createElementNS(NS.HTML, "div");
    document.documentElement.appendChild(root);
    createApp(component).mount(root);

    document
        .querySelectorAll("amber-embed[mode~='picker']")
        .forEach((node: HTMLElement) => {
            node.addEventListener("contextmenu", e => {
                cm.open(
                    e,
                    [
                        { label: "Öffnen", icon: "📂", shortcut: "Enter", onClick: () => console.log("open") },
                        { label: "Umbenennen", icon: "✏️", onClick: () => console.log("rename") },
                        { label: "Löschen", icon: "🗑️", shortcut: "Del", onClick: () => console.log("delete") },
                        { label: "Deaktiviert", icon: "🚫", disabled: true },
                    ],
                    { id: 123 } // payload (optional)
                );
            });
        });
}

Bootstrap.run(bootstrap);