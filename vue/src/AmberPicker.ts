"use strict";

import { createApp } from "vue";
import ContextMenu, { TitleItem, MenuItem, SelectItem, SeparatorItem, ToggleItem, MapGroup, MapOption, MapItem } from "./components/ContextMenu";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import AmberAPI from "/slothsoft@amber/js/AmberAPI";
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

function instantiateItem(node: Element, groups: MapGroup[]): MapItem {
    const input = node.querySelector("input") as HTMLInputElement;

    return {
        type: "map",
        get: (): number => parseInt(input.value),
        set: (value: number) => {
            const data = String(value);
            node.setAttribute("value", data);
            input.value = data;
        },
        groups,
    };
}

async function instantiate(node: Element): Promise<MenuItem[]> {
    const type = node.getAttribute("type");

    switch (type) {
        case "portrait":
            const portraitGroups = await AmberAPI
                .getAmberdataDocument("lib.portraits")
                .then(buildPortraitGroups);

            return [
                instantiateTitle("Portrait"),
                instantiateSeparator(),
                instantiateItem(node.querySelector("amber-portrait-id"), portraitGroups),
            ];
        case "item":
            const itemGroups = await AmberAPI
                .getAmberdataDocument("lib.items")
                .then(buildItemGroups);

            return [
                instantiateTitle("Gegenstand"),
                instantiateSeparator(),
                instantiateItem(node.querySelector("amber-item-id"), itemGroups),
                instantiateSeparator(),
                instantiateNumber(node.querySelector("amber-item-amount"), "Anzahl:"),
                instantiateToggle(node.querySelector("amber-identified"), "Ist identifiziert:"),
                instantiateToggle(node.querySelector("amber-broken"), "Ist zerbrochen:"),
                instantiateNumber(node.querySelector("amber-item-charge"), "Mag. Ladungen:"),
                instantiateNumber(node.querySelector("amber-item-recharges"), "Bisherige Aufladungen:"),
            ];
        default:
            throw new Error(`Unknown type "${type}"`);
    }
}

function buildPortraitGroups(doc: XMLDocument): MapGroup[] {
    const cats = Array.from(doc.getElementsByTagNameNS(NS.AMBER_AMBERDATA, "portrait-category"));

    const groups: MapGroup[] = cats.map((cat) => {
        const label = cat.getAttribute("name");
        const items = Array.from(cat.getElementsByTagNameNS(NS.AMBER_AMBERDATA, "portrait"));

        const options: MapOption[] = items
            .map((it) => ({
                value: parseInt(it.getAttribute("id")),
                label: `Portrait #${it.getAttribute("id")}`,
            }));

        return { label, options };
    });

    return groups;
}

function buildItemGroups(doc: XMLDocument): MapGroup[] {
    const cats = Array.from(doc.getElementsByTagNameNS(NS.AMBER_AMBERDATA, "item-category"));

    const groups: MapGroup[] = cats.map((cat) => {
        const label = cat.getAttribute("name");
        const items = Array.from(cat.getElementsByTagNameNS(NS.AMBER_AMBERDATA, "item"));

        const options: MapOption[] = items
            .map((it) => ({
                value: parseInt(it.getAttribute("id")),
                label: it.getAttribute("name"),
            }));

        return { label, options };
    });

    groups.unshift({ label: "", options: [{ value: 0, label: "-" }] });

    return groups;
}

function bootstrap(): void {
    const cm = new ContextMenu(instantiate);

    const root = document.createElementNS(NS.HTML, "div");
    root.setAttribute("class", "amber-picker amber-text amber-text--silver");
    document.documentElement.appendChild(root);
    createApp(cm.component).mount(root);

    document.querySelectorAll("amber-embed[mode~='picker']").forEach((node: Element) => cm.registerMenu(node));
}

Bootstrap.run(bootstrap);