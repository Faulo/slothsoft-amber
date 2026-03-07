"use strict";

import { createApp } from "vue";
import ContextMenu, { TitleItem, MenuItem, SelectItem, SeparatorItem, ToggleItem } from "./components/ContextMenu";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

const RANGE_0_99_PLUS_255: string[] = [
    ...Array.from({ length: 100 }, (_, i) => String(i)),
    "255",
];

function instantiateTitle(label: string): TitleItem {
    return {
        type: "title",
        label,
    };
}

function instantiateSeparator(): SeparatorItem {
    return {
        type: "separator",
    };
}

function instantiateToggle(node: Element, label: string): ToggleItem {
    const input = node.querySelector("input") as HTMLInputElement;

    return {
        type: "toggle",
        label,
        get: (): boolean => input.value === "1",
        set: (value: boolean) => {
            const data = value ? "1" : "";
            node.setAttribute("value", data);
            input.value = data;
        }
    };
}

function instantiateNumber(node: Element, label: string): SelectItem {
    const input = node.querySelector("input") as HTMLInputElement;

    return {
        type: "select",
        label,
        get: (): string => input.value,
        set: (value: string) => {
            node.setAttribute("value", value);
            input.value = value;
        },
        items: RANGE_0_99_PLUS_255,
    };
}

function instantiate(node: Element): MenuItem[] {
    return [
        instantiateTitle("Gegenstand"),
        instantiateSeparator(),
        instantiateNumber(node.querySelector("amber-item-amount"), "Anzahl:"),
        instantiateToggle(node.querySelector("amber-identified"), "Ist identifiziert:"),
        instantiateToggle(node.querySelector("amber-broken"), "Ist zerbrochen:"),
        instantiateNumber(node.querySelector("amber-item-charge"), "Mag. Ladungen:"),
    ];
}

function bootstrap(): void {
    const cm = new ContextMenu(instantiate);

    const root = document.createElementNS(NS.HTML, "div");
    root.setAttribute("class", "amber-picker amber-text");
    document.documentElement.appendChild(root);
    createApp(cm.component).mount(root);

    document.querySelectorAll("amber-embed[mode~='picker']").forEach((node: Element) => cm.registerMenu(node));
}

Bootstrap.run(bootstrap);