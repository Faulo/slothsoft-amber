"use strict";

import Bootstrap from "/slothsoft@farah/js/Bootstrap";
import AmberAPI from "/slothsoft@amber/js/AmberAPI";

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
    #character;

    constructor(fieldsetNode) {
        this.#fieldsetNode = fieldsetNode;

        for (let node of fieldsetNode.querySelectorAll("*[data-editor-action]")) {
            const action = node.getAttribute("data-editor-action");
            switch (node.localName) {
                case "input":
                case "select":
                    node.addEventListener("change", () => this.#execute(node, action), false);
                    break;
                case "button":
                    node.addEventListener("click", () => this.#execute(node, action), false);
                    break;
                default:
                    alert(`Unknown node "${node.localName}"`);
                    break;
            }
            node.disabled = false;
        }

        for (let node of fieldsetNode.querySelectorAll("*[data-editor-onload]")) {
            const action = node.getAttribute("data-editor-onload");
            this.#execute(node, action);
        }
    }

    async #execute(buttonNode, action) {
        buttonNode.disabled = true;
        buttonNode.title = "";
        switch (action) {
            case "apply-member-count":
                this.#applyMemberCount(buttonNode.value);
                break;
            case "roll-character":
                this.#applyRace();
                this.#applyClass();
                this.#rollStats();
                break;
            case "roll-stats":
                this.#rollStats();
                break;
            case "apply-race":
                this.#applyRace();
                break;
            case "apply-class":
                this.#applyClass();
                break;
            case "apply-portrait":
                this.#applyPortrait(buttonNode.value);
                break;
            case "apply-equipment":
                buttonNode.title = "Berechnet...";
                buttonNode.title = await this.#applyEquipment();
                alert(buttonNode.title);
                break;
            default:
                alert(`Unknown action "${action}"`);
                break;
        }
        buttonNode.disabled = false;
    }
    #applyMemberCount(count) {
        const selectNodes = this.#fieldsetNode.querySelectorAll("select");
        for (let i = 0; i < selectNodes.length; i++) {
            const characterIndex = i + 1;
            const selectNode = selectNodes[i];
            selectNode.selectedIndex = characterIndex <= count ? characterIndex : 0;
        }

        const checkboxNodes = this.#fieldsetNode.querySelectorAll("input[type='checkbox']");
        for (let i = 0; i < checkboxNodes.length; i++) {
            const characterIndex = i + 1;
            const checkboxNode = checkboxNodes[i];
            checkboxNode.checked = characterIndex <= count;
        }

        const characterNodes = this.#fieldsetNode.parentNode.querySelectorAll(".amber-editor__party>*");
        for (let i = 0; i < characterNodes.length; i++) {
            const characterIndex = i + 1;
            const characterNode = characterNodes[i];
            characterNode.disabled = characterIndex > count;
        }
    }
    #rollStats() {
        this.#character ??= new AmberCharacter(this.#fieldsetNode);

        const mappings = {};

        //Attribute
        for (let key in this.#character.attributes) {
            mappings[key] = this.#character.attributes[key].querySelectorAll("input");
        }

        //Skills
        for (let key in this.#character.skills) {
            mappings[key] = this.#character.skills[key].querySelectorAll("input");
        }

        const attributes = ["Stärke", "Intelligenz", "Geschicklichkeit", "Schnelligkeit", "Konstitution", "Karisma", "Glück", "Anti-Magie"];
        const specials = ["Anti-Magie", "Kritische Treffer", "Fallen Finden", "Fallen Entschärfen", "Schlösser Knacken", "Suchen"];
        const zeros = ["Schwimmen"];

        for (let key in mappings) {
            const currentInput = mappings[key][0];
            const maxInput = mappings[key][2];

            const max = parseInt(maxInput.value);

            const isZero = zeros.includes(key);

            if (max === 0 || isZero) {
                currentInput.value = 0;
            } else {
                const isAttribute = attributes.includes(key);
                const isSpecial = specials.includes(key);

                let amount, type, modifier;

                if (isAttribute) {
                    amount = 2;
                    const step = Math.floor(max / amount);

                    if (isSpecial) {
                        type = step + 1;
                    } else {
                        type = step;
                    }

                    modifier = (max - amount * type);
                } else {
                    const step = 5;
                    amount = Math.max(1, Math.floor(0.8 * max / step));

                    if (isSpecial) {
                        type = step + 1;
                        modifier = -amount;
                    } else {
                        type = step;
                        modifier = 0;
                    }
                }

                currentInput.value = this.#roll(amount, type, modifier);

                if (currentInput.value === maxInput.value) {
                    console.log(`High roll for ${key}: ${max}`);
                }
            }
        }
    }

    #roll(amount, type, modifier) {
        let result = amount + modifier;
        for (let i = 0; i < amount; i++) {
            result += Math.floor(Math.random() * type);
        }

        // const modifierText = modifier > 0 ? `+${modifier}` : modifier < 0 ? modifier.toString() : "";
        // console.log(`${amount}d${type}${modifierText}=${result}`);

        if (result < 0) {
            result = 0;
        }

        return result;
    }

    #applyPortrait(portraitId) {
        this.#character ??= new AmberCharacter(this.#fieldsetNode);

        const input = this.#character.inputs["portrait-id"];
        input.value = portraitId;
        input.parentNode.setAttribute("value", portraitId);
        input.parentNode.parentNode.setAttribute("id", portraitId);
    }

    #applyRace() {
        this.#character ??= new AmberCharacter(this.#fieldsetNode);

        const raceId = this.#character.inputs["race"].value;

        if (AmberCharacter.races[raceId]) {
            const race = AmberCharacter.races[raceId];

            //Attribute
            for (let key in this.#character.attributes) {
                const input = this.#character.attributes[key].querySelector("input[data-name='maximum']");
                input.value = race[key];
            }

            const mappings = {};
            mappings["age--maximum"] = "Alter";
            for (let key in mappings) {
                this.#character.inputs[key].value = race[mappings[key]];
            }
        }
    }

    #applyClass() {
        this.#character ??= new AmberCharacter(this.#fieldsetNode);

        const classId = this.#character.inputs["class"].value;

        if (AmberCharacter.classes[classId]) {
            const klasse = AmberCharacter.classes[classId];

            //Skills
            for (let key in this.#character.skills) {
                const input = this.#character.skills[key].querySelector("input[data-name='maximum']");
                input.value = klasse[key];
            }

            const mappings = {};
            mappings["hit-points--current"] = "Lebenspunkte";
            mappings["hit-points--maximum"] = "Lebenspunkte";
            mappings["hp-per-level"] = "Lebenspunkte";
            mappings["training-points"] = "Trainingspunkte";
            mappings["tp-per-level"] = "Trainingspunkte";
            mappings["spell-points--current"] = "Spruchpunkte";
            mappings["spell-points--maximum"] = "Spruchpunkte";
            mappings["sp-per-level"] = "Spruchpunkte";
            mappings["spelllearn-points"] = "Spruchlesepunkte";
            mappings["slp-per-level"] = "Spruchlesepunkte";
            mappings["apr-per-level"] = "AttackenProRundeProLevel";

            for (let key in mappings) {
                this.#character.inputs[key].value = klasse[mappings[key]];
            }

            // Zauberschule
            for (let i = 0; i < this.#character.spellbooks.length; i++) {
                this.#character.spellbooks[i].checked = i === klasse.Zauberschule - 1;
            }
        }
    }

    async #applyEquipment() {
        this.#character ??= new AmberCharacter(this.#fieldsetNode);

        const mappings = {};
        mappings["lp-max"] = this.#character.inputs["hit-points--maximum-mod"];
        mappings["sp-max"] = this.#character.inputs["spell-points--maximum-mod"];
        mappings["hands"] = this.#character.inputs["hand"];
        mappings["fingers"] = this.#character.inputs["finger"];
        mappings["damage"] = this.#character.inputs["attack"];
        mappings["armor"] = this.#character.inputs["defense"];
        mappings["magic-weapon"] = this.#character.inputs["magic-attack"];
        mappings["magic-armor"] = this.#character.inputs["magic-defense"];

        //Attribute
        for (let key in this.#character.attributes) {
            mappings[key] = this.#character.attributes[key].querySelector("input[data-name='current-mod']");
        }

        //Skills
        for (let key in this.#character.skills) {
            mappings[key] = this.#character.skills[key].querySelector("input[data-name='current-mod']");
        }

        mappings["weight"] = this.#character.inputs["weight"];

        const data = {};
        for (let key in mappings) {
            data[key] = 0;
        }

        const itemIds = [];
        for (let itemPicker of this.#character.equippedItems) {
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
        const goldNode = this.#character.inputs["gold"];
        if (goldNode) {
            data["weight"] += WEIGHT_OF_GOLD * parseInt(goldNode.value);
        }

        const foodNode = this.#character.inputs["food"];
        if (foodNode) {
            data["weight"] += WEIGHT_OF_FOOD * parseInt(foodNode.value);
        }

        for (let itemPicker of this.#character.carriedItems) {
            const itemId = parseInt(itemPicker.querySelector("amber-item-id input").value);
            const itemAmount = parseInt(itemPicker.querySelector("amber-item-amount input").value);
            if (itemId) {
                const itemNode = await AmberAPI.getAmberdataItem(itemId);
                data["weight"] += itemAmount * parseInt(itemNode.getAttribute("weight"))
            }
        }

        const result = [];

        for (let key in data) {
            if (!mappings[key]) {
                alert(`Unknown data key "${key}"`);
                continue;
            }

            if (mappings[key].value != data[key]) {
                result.push(`${key}: ${mappings[key].value} => ${data[key]}`);
                mappings[key].value = data[key];
            }
        }

        return result.length > 0 ? "Änderungen: " + result.join("; ") : "Keine Änderungen nötig!";
    }
}

class AmberCharacter {
    static races = [
        { // Mensch
            "Alter": 80,
            "Stärke": 50,
            "Intelligenz": 50,
            "Geschicklichkeit": 50,
            "Schnelligkeit": 50,
            "Konstitution": 50,
            "Karisma": 50,
            "Glück": 50,
            "Anti-Magie": 0,
        },
        { // Elf
            "Alter": 999,
            "Stärke": 35,
            "Intelligenz": 90,
            "Geschicklichkeit": 70,
            "Schnelligkeit": 70,
            "Konstitution": 35,
            "Karisma": 90,
            "Glück": 50,
            "Anti-Magie": 30,
        },
        { // Zwerg
            "Alter": 750,
            "Stärke": 90,
            "Intelligenz": 35,
            "Geschicklichkeit": 40,
            "Schnelligkeit": 40,
            "Konstitution": 90,
            "Karisma": 35,
            "Glück": 50,
            "Anti-Magie": 5,
        },
        { // Gnom
            "Alter": 500,
            "Stärke": 50,
            "Intelligenz": 75,
            "Geschicklichkeit": 60,
            "Schnelligkeit": 60,
            "Konstitution": 40,
            "Karisma": 20,
            "Glück": 60,
            "Anti-Magie": 10,
        },
        { // Halb-Elf
            "Alter": 250,
            "Stärke": 40,
            "Intelligenz": 70,
            "Geschicklichkeit": 60,
            "Schnelligkeit": 60,
            "Konstitution": 40,
            "Karisma": 70,
            "Glück": 50,
            "Anti-Magie": 15,
        },
        { // Sylphe
            "Alter": 800,
            "Stärke": 20,
            "Intelligenz": 25,
            "Geschicklichkeit": 95,
            "Schnelligkeit": 95,
            "Konstitution": 25,
            "Karisma": 95,
            "Glück": 75,
            "Anti-Magie": 50,
        },
        { // Feline
            "Alter": 85,
            "Stärke": 70,
            "Intelligenz": 40,
            "Geschicklichkeit": 80,
            "Schnelligkeit": 80,
            "Konstitution": 50,
            "Karisma": 40,
            "Glück": 50,
            "Anti-Magie": 0,
        },
        { // Moraner
            "Alter": 65,
            "Stärke": 40,
            "Intelligenz": 95,
            "Geschicklichkeit": 75,
            "Schnelligkeit": 85,
            "Konstitution": 40,
            "Karisma": 15,
            "Glück": 50,
            "Anti-Magie": 60,
        },
        { // Thalioner
            "Alter": 999,
            "Stärke": 99,
            "Intelligenz": 99,
            "Geschicklichkeit": 99,
            "Schnelligkeit": 99,
            "Konstitution": 99,
            "Karisma": 99,
            "Glück": 99,
            "Anti-Magie": 99,
        }
    ];

    static classes = [
        {// Abenteurer
            "Lebenspunkte": 10,
            "Spruchpunkte": 8,
            "Trainingspunkte": 6,
            "Spruchlesepunkte": 5,
            "AttackenProRundeProLevel": 12,
            "Zauberschule": 2,
            "Attacke": 80,
            "Parade": 75,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 25,
            "Fallen Entschärfen": 25,
            "Schlösser Knacken": 25,
            "Suchen": 25,
            "Spruchrollen Lesen": 50,
            "Magie benutzen": 50,
        },
        {// Krieger
            "Lebenspunkte": 16,
            "Spruchpunkte": 0,
            "Trainingspunkte": 8,
            "Spruchlesepunkte": 0,
            "AttackenProRundeProLevel": 5,
            "Zauberschule": 0,
            "Attacke": 95,
            "Parade": 95,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 0,
            "Fallen Entschärfen": 0,
            "Schlösser Knacken": 0,
            "Suchen": 10,
            "Spruchrollen Lesen": 0,
            "Magie benutzen": 0,
        },
        {// Paladin
            "Lebenspunkte": 14,
            "Spruchpunkte": 6,
            "Trainingspunkte": 10,
            "Spruchlesepunkte": 3,
            "AttackenProRundeProLevel": 8,
            "Zauberschule": 1,
            "Attacke": 85,
            "Parade": 65,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 0,
            "Fallen Entschärfen": 0,
            "Schlösser Knacken": 0,
            "Suchen": 30,
            "Spruchrollen Lesen": 50,
            "Magie benutzen": 50,
        },
        {// Dieb
            "Lebenspunkte": 8,
            "Spruchpunkte": 0,
            "Trainingspunkte": 14,
            "Spruchlesepunkte": 0,
            "AttackenProRundeProLevel": 0,
            "Zauberschule": 0,
            "Attacke": 50,
            "Parade": 50,
            "Schwimmen": 95,
            "Kritische Treffer": 5,
            "Fallen Finden": 95,
            "Fallen Entschärfen": 95,
            "Schlösser Knacken": 95,
            "Suchen": 75,
            "Spruchrollen Lesen": 0,
            "Magie benutzen": 0,
        },
        {// Ranger
            "Lebenspunkte": 10,
            "Spruchpunkte": 6,
            "Trainingspunkte": 12,
            "Spruchlesepunkte": 3,
            "AttackenProRundeProLevel": 10,
            "Zauberschule": 3,
            "Attacke": 70,
            "Parade": 60,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 40,
            "Fallen Entschärfen": 40,
            "Schlösser Knacken": 15,
            "Suchen": 95,
            "Spruchrollen Lesen": 50,
            "Magie benutzen": 50,
        },
        {// Heiler
            "Lebenspunkte": 6,
            "Spruchpunkte": 16,
            "Trainingspunkte": 10,
            "Spruchlesepunkte": 10,
            "AttackenProRundeProLevel": 0,
            "Zauberschule": 1,
            "Attacke": 25,
            "Parade": 40,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 0,
            "Fallen Entschärfen": 0,
            "Schlösser Knacken": 0,
            "Suchen": 0,
            "Spruchrollen Lesen": 95,
            "Magie benutzen": 95,
        },
        {// Alchemist
            "Lebenspunkte": 6,
            "Spruchpunkte": 16,
            "Trainingspunkte": 10,
            "Spruchlesepunkte": 10,
            "AttackenProRundeProLevel": 15,
            "Zauberschule": 2,
            "Attacke": 25,
            "Parade": 40,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 0,
            "Fallen Entschärfen": 0,
            "Schlösser Knacken": 0,
            "Suchen": 0,
            "Spruchrollen Lesen": 95,
            "Magie benutzen": 95,
        },
        {// Mystiker
            "Lebenspunkte": 6,
            "Spruchpunkte": 16,
            "Trainingspunkte": 10,
            "Spruchlesepunkte": 10,
            "AttackenProRundeProLevel": 15,
            "Zauberschule": 3,
            "Attacke": 25,
            "Parade": 40,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 0,
            "Fallen Entschärfen": 0,
            "Schlösser Knacken": 0,
            "Suchen": 0,
            "Spruchrollen Lesen": 95,
            "Magie benutzen": 95,
        },
        {// Magier
            "Lebenspunkte": 6,
            "Spruchpunkte": 16,
            "Trainingspunkte": 10,
            "Spruchlesepunkte": 10,
            "AttackenProRundeProLevel": 0,
            "Zauberschule": 4,
            "Attacke": 35,
            "Parade": 35,
            "Schwimmen": 95,
            "Kritische Treffer": 0,
            "Fallen Finden": 0,
            "Fallen Entschärfen": 0,
            "Schlösser Knacken": 0,
            "Suchen": 0,
            "Spruchrollen Lesen": 99,
            "Magie benutzen": 99,
        },
    ];

    inputs = {};
    attributes = {};
    skills = {};
    equippedItems = [];
    carriedItems = [];
    spellbooks = [];
    constructor(characterNode) {

        const simpleKeys = [
            "race", "class", "portrait-id",
            "hp-per-level", "sp-per-level", "tp-per-level", "slp-per-level", "apr-per-level",
            "training-points", "spelllearn-points",
            "experience", "level", "attacks-per-round",
            "hand", "finger", "defense", "attack", "magic-attack", "magic-defense",
            "gold", "food", "weight"
        ];

        for (let key of simpleKeys) {
            this.inputs[key] = characterNode.querySelector(`*[data-name="${key}"]`);
            if (!this.inputs[key]) {
                console.warn(`Failed to find input with data-name "${key}" in node ${characterNode}`);
            }
        }

        this.inputs["age--current"] = characterNode.querySelector("*[data-name='age'] input[data-name='current']");
        this.inputs["age--maximum"] = characterNode.querySelector("*[data-name='age'] input[data-name='maximum']");

        this.inputs["hit-points--current"] = characterNode.querySelector("*[data-name='hit-points'] input[data-name='current']");
        this.inputs["hit-points--maximum"] = characterNode.querySelector("*[data-name='hit-points'] input[data-name='maximum']");
        this.inputs["hit-points--maximum-mod"] = characterNode.querySelector("*[data-name='hit-points'] input[data-name='maximum-mod']");

        this.inputs["spell-points--current"] = characterNode.querySelector("*[data-name='spell-points'] input[data-name='current']");
        this.inputs["spell-points--maximum"] = characterNode.querySelector("*[data-name='spell-points'] input[data-name='maximum']");
        this.inputs["spell-points--maximum-mod"] = characterNode.querySelector("*[data-name='spell-points'] input[data-name='maximum-mod']");

        const attributes = characterNode.querySelectorAll(".attributes tr");
        this.attributes["Stärke"] = attributes[0];
        this.attributes["Intelligenz"] = attributes[1];
        this.attributes["Geschicklichkeit"] = attributes[2];
        this.attributes["Schnelligkeit"] = attributes[3];
        this.attributes["Konstitution"] = attributes[4];
        this.attributes["Karisma"] = attributes[5];
        this.attributes["Glück"] = attributes[6];
        this.attributes["Anti-Magie"] = attributes[7];

        const skills = characterNode.querySelectorAll(".skills tr");
        this.skills["Attacke"] = skills[0];
        this.skills["Parade"] = skills[1];
        this.skills["Schwimmen"] = skills[2];
        this.skills["Kritische Treffer"] = skills[3];
        this.skills["Fallen Finden"] = skills[4];
        this.skills["Fallen Entschärfen"] = skills[5];
        this.skills["Schlösser Knacken"] = skills[6];
        this.skills["Suchen"] = skills[7];
        this.skills["Spruchrollen Lesen"] = skills[8];
        this.skills["Magie benutzen"] = skills[9];

        this.equippedItems.push(...characterNode.querySelectorAll(".equipment amber-embed"));
        this.carriedItems.push(...characterNode.querySelectorAll(".inventory amber-embed"));

        this.spellbooks.push(...characterNode.querySelectorAll('*[data-name="spellbooks"] input[type="checkbox"]'));
    }
}

Bootstrap.run(bootstrap);