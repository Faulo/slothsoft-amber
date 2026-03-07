"use strict";

import { reactive, onMounted, onBeforeUnmount, h, Reactive, watch, VNode } from "vue";

export type TitleItem = {
    type: "title";
    label: string
};
export type ToggleItem = {
    type: "toggle";
    label: string;
    get: () => boolean;
    set: (value: boolean) => void;
};
export type SelectItem = {
    type: "select";
    label: string;
    get: () => string;
    set: (value: string) => void;
    items: string[];
};
export type SeparatorItem = {
    type: "separator"
};
export type MapItem = {
    type: "map";
    get: () => number;
    set: (value: number) => void;
    groups: MapGroup[];
};
export type MapOption = { value: number; label: string };
export type MapGroup = { label: string; options: MapOption[] };

export type MenuItem = TitleItem | ToggleItem | SelectItem | SeparatorItem | MapItem;

const itemRenderers = {
    title: (item: TitleItem): VNode => {
        return h(
            "h1",
            {
                class: "context-menu__item context-menu__item--title"
            },
            item.label
        );
    },
    separator: (_: SeparatorItem): VNode => {
        return h(
            "hr",
            {
                class: "context-menu__item context-menu__item--separator"
            },
        );
    },
    toggle: (item: ToggleItem): VNode => {
        return h(
            "label",
            {
                class: "context-menu__item context-menu__item--toggle"
            },
            [
                item.label,
                h(
                    "input",
                    {
                        type: "checkbox",
                        class: "context-menu__input context-menu__input--toggle",
                        checked: item.get(),
                        onChange: (eve: Event) => {
                            const input = eve.currentTarget as HTMLInputElement;
                            item.set(input.checked);
                        }
                    }
                )
            ]
        );
    },
    select: (item: SelectItem): VNode => {
        return h(
            "label",
            {
                class: "context-menu__item context-menu__item--select"
            },
            [
                item.label,
                h(
                    "select",
                    {
                        class: "context-menu__input context-menu__input--select",
                        value: item.get(),
                        onChange: (eve: Event) => {
                            const input = eve.currentTarget as HTMLSelectElement;
                            item.set(input.value);
                        },
                    },
                    item.items.map((v) => h("option", { value: v }, v))
                ),
            ]
        );
    },
    "map": (item: MapItem): VNode => {
        return h(
            "label",
            { class: "context-menu__item context-menu__item--map" },
            [
                h(
                    "select",
                    {
                        class: "context-menu__input context-menu__input--map",
                        value: item.get(),
                        onChange: (eve: Event) => {
                            const input = eve.currentTarget as HTMLSelectElement;
                            item.set(parseInt(input.value));
                        },
                    },
                    item.groups.map((g) =>
                        h(
                            "optgroup",
                            { label: g.label },
                            g.options.map((o) => h("option", { value: o.value }, o.label))
                        )
                    )
                ),
            ]
        );
    },
};

class State {
    open: boolean = false;
    items: MenuItem[] = [];
    x: number = 0;
    y: number = 0;
}

export default class ContextMenu {
    public readonly name = "ContextMenu";
    private instantiate: Function;
    private state: Reactive<State>;

    public readonly component: Object;

    constructor(instantiate: Function) {
        this.instantiate = instantiate;
        this.state = reactive(new State());

        this.component = { setup: () => this.setup() };
    }

    private setup(): Function {
        onMounted(() => {
            window.addEventListener("pointerdown", this.onPointerDown);
            window.addEventListener("keydown", this.onKeyDown);
        });

        onBeforeUnmount(() => {
            window.removeEventListener("pointerdown", this.onPointerDown);
            window.removeEventListener("keydown", this.onKeyDown);
        });

        return () => {
            if (!this.state.open) {
                return null;
            }

            return h("div", {
                class: "context-menu",
                style: {
                    position: "fixed",
                    left: `${this.state.x}px`,
                    top: `${this.state.y}px`
                },
                onPointerdown: (eve: PointerEvent) => eve.stopPropagation(),
            }, this.state.items.map(item => itemRenderers[item.type](item as any)));
        };
    }

    public registerMenu(node: Element): void {
        node.addEventListener("contextmenu", this.onOpenMenu);
    }

    public unregisterMenu(node: Element): void {
        node.removeEventListener("contextmenu", this.onOpenMenu);
    }

    private readonly onPointerDown = (eve: PointerEvent) => {
        this.close();
    }

    private readonly onKeyDown = (eve: KeyboardEvent) => {
        if (!this.state.open) {
            return;
        }

        switch (eve.key) {
            case "Escape":
                eve.preventDefault();
                this.close();
                break;
        }
    }

    private readonly onOpenMenu = (eve: MouseEvent) => {
        eve.preventDefault();
        this.open(eve.currentTarget as Element, eve.clientX, eve.clientY);
    }

    public async open(target: Element, x: number, y: number) {
        if (this.state.open) {
            this.close();
        }

        this.state.items = await this.instantiate(target);
        this.state.open = true;
        this.state.x = x;
        this.state.y = y;
    }

    public close(): void {
        this.state.open = false;
        this.state.items = [];
    }
}