"use strict";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import AmberAPI from "/slothsoft@amber/js/AmberAPI";
import DOM from "/slothsoft@farah/js/DOM";
import { NS } from "/slothsoft@farah/js/XMLNamespaces";

const WEIGHT_OF_GOLD = 5;
const WEIGHT_OF_FOOD = 250;

function bootstrap() {
    for (let node of window.document.querySelectorAll("form")) {
        new AmberEditor(node);
    }
}

export default class AmberEditor {
    constructor(formNode) {
        for (let node of formNode.querySelectorAll("fieldset")) {
            new AmberEditorPage(node);
        }
    }
}

class AmberEditorPage {
    #fieldsetNode;

    constructor(fieldsetNode) {
        this.#fieldsetNode = fieldsetNode;

        for (let node of fieldsetNode.querySelectorAll("*[data-editor-action]")) {
            const action = node.getAttribute("data-editor-action");
            node.addEventListener("click", () => this.#execute(node, action), false);
            node.disabled = false;
        }
    }

    async #execute(buttonNode, action) {
        buttonNode.disabled = true;
        buttonNode.title = "Berechnet...";
        switch (action) {
            case "apply-equipment":
                buttonNode.title = await this.#applyEquipment();
                alert(buttonNode.title);
                break;
            default:
                alert(`Unknown action "${action}"`);
                break;
        }
        buttonNode.disabled = false;
    }

    async #applyEquipment() {
        const characterNode = this.#fieldsetNode;

        const mappings = {};
        mappings["lp-max"] = characterNode.querySelectorAll("input[data-name='maximum-mod']")[0];
        mappings["sp-max"] = characterNode.querySelectorAll("input[data-name='maximum-mod']")[1];
        mappings["hands"] = characterNode.querySelector("select[data-name='hand']");
        mappings["fingers"] = characterNode.querySelector("select[data-name='finger']");
        mappings["damage"] = characterNode.querySelector("input[data-name='attack']");
        mappings["armor"] = characterNode.querySelector("input[data-name='defense']");
        mappings["magic-weapon"] = characterNode.querySelector("input[data-name='magic-attack']");
        mappings["magic-armor"] = characterNode.querySelector("input[data-name='magic-defense']");

        //Attribute
        mappings["Stärke"] = characterNode.querySelectorAll(".attributes tr")[0].querySelector("input[data-name='current-mod']");
        mappings["Intelligenz"] = characterNode.querySelectorAll(".attributes tr")[1].querySelector("input[data-name='current-mod']");
        mappings["Geschicklichkeit"] = characterNode.querySelectorAll(".attributes tr")[2].querySelector("input[data-name='current-mod']");
        mappings["Schnelligkeit"] = characterNode.querySelectorAll(".attributes tr")[3].querySelector("input[data-name='current-mod']");
        mappings["Konstitution"] = characterNode.querySelectorAll(".attributes tr")[4].querySelector("input[data-name='current-mod']");
        mappings["Karisma"] = characterNode.querySelectorAll(".attributes tr")[5].querySelector("input[data-name='current-mod']");
        mappings["Glück"] = characterNode.querySelectorAll(".attributes tr")[6].querySelector("input[data-name='current-mod']");
        mappings["Anti-Magie"] = characterNode.querySelectorAll(".attributes tr")[7].querySelector("input[data-name='current-mod']");

        //Skills
        mappings["Attacke"] = characterNode.querySelectorAll(".skills tr")[0].querySelector("input[data-name='current-mod']");
        mappings["Parade"] = characterNode.querySelectorAll(".skills tr")[1].querySelector("input[data-name='current-mod']");
        mappings["Schwimmen"] = characterNode.querySelectorAll(".skills tr")[2].querySelector("input[data-name='current-mod']");
        mappings["Kritische Treffer"] = characterNode.querySelectorAll(".skills tr")[3].querySelector("input[data-name='current-mod']");
        mappings["Fallen Finden"] = characterNode.querySelectorAll(".skills tr")[4].querySelector("input[data-name='current-mod']");
        mappings["Fallen Entschärfen"] = characterNode.querySelectorAll(".skills tr")[5].querySelector("input[data-name='current-mod']");
        mappings["Schlösser Knacken"] = characterNode.querySelectorAll(".skills tr")[6].querySelector("input[data-name='current-mod']");
        mappings["Suchen"] = characterNode.querySelectorAll(".skills tr")[7].querySelector("input[data-name='current-mod']");
        mappings["Spruchrollen Lesen"] = characterNode.querySelectorAll(".skills tr")[8].querySelector("input[data-name='current-mod']");
        mappings["Magie Benutzen"] = characterNode.querySelectorAll(".skills tr")[9].querySelector("input[data-name='current-mod']");

        mappings["weight"] = characterNode.querySelector("input[data-name='weight']");

        const data = {};
        for (let key in mappings) {
            data[key] = 0;
        }

        const itemPickers = characterNode.querySelectorAll(".equipment amber-embed");
        const itemIds = [];
        for (let itemPicker of itemPickers) {
            const itemId = parseInt(itemPicker.querySelector("amber-item-id input").value);
            if (itemId) {
                itemIds.push(itemId);
            }
        }

        const itemNodes = await AmberAPI.getAmberdataItems(itemIds);
        itemNodes.forEach(
            (itemNode) => {
                for (let key in data) {
                    if (itemNode.hasAttribute(key)) {
                        data[key] += parseInt(itemNode.getAttribute(key));
                    }
                }
                if (parseInt(itemNode.getAttribute("attribute-value"))) {
                    data[itemNode.getAttribute("attribute-type")] += parseInt(itemNode.getAttribute("attribute-value"));
                }
                if (parseInt(itemNode.getAttribute("skill-value"))) {
                    data[itemNode.getAttribute("skill-type")] += parseInt(itemNode.getAttribute("skill-value"));
                }
            }
        );

        // Gewichtsberechnung
        const goldNode = characterNode.querySelector("input[data-name='gold']");
        if (goldNode) {
            data["weight"] += WEIGHT_OF_GOLD * parseInt(goldNode.value);
        }

        const foodNode = characterNode.querySelector("input[data-name='food']");
        if (foodNode) {
            data["weight"] += WEIGHT_OF_FOOD * parseInt(foodNode.value);
        }

        for (let itemPicker of characterNode.querySelectorAll(".inventory amber-embed")) {
            const itemId = parseInt(itemPicker.querySelector("amber-item-id input").value);
            const itemAmount = parseInt(itemPicker.querySelector("amber-item-amount input").value);
            if (itemId) {
                const itemNode = await AmberAPI.getAmberdataItem(itemId);
                data["weight"] += itemAmount * parseInt(itemNode.getAttribute("weight"))
            }
        }

        const result = [];

        for (let key in mappings) {
            if (mappings[key]) {
                if (mappings[key].value != data[key]) {
                    result.push(`${key}: ${mappings[key].value} => ${data[key]}`);
                    mappings[key].value = data[key];
                }
            }
        }

        return result.length > 0 ? "Änderungen: " + result.join("; ") : "Keine Änderungen nötig!";
    }
}

Bootstrap.run(bootstrap);