"use strict";

import { reactive, ref, nextTick, onMounted, onBeforeUnmount, h, Teleport, Reactive, watch } from "vue";

type MenuItem =
    | { type: "action"; id: string; label: string }
    | { type: "toggle"; id: string; label: string }
    | { type: "separator" };

class State {
    open: boolean = false;
    target: Element | null = null;
    x: number = 0;
    y: number = 0;
    items: MenuItem[] = [];
}

export default class ContextMenu {
    public readonly name = "ContextMenu";
    private instantiate: Function;
    private bind: Function;
    private state: Reactive<State>;

    public readonly component: Object;

    constructor(instantiate: Function, bind: Function) {
        this.instantiate = instantiate;
        this.bind = bind;
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

        watch(
            [() => this.state.open, () => this.state.target],
            ([open, target]) => {
                if (open && target) {
                    this.state.items = this.instantiate(target);
                } else {
                    this.state.items = [];
                }
            }
        );

        return () => {
            if (!this.state.open) {
                return null;
            }

            if (!this.state.target) {
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
            }, "Mein Kontextmenü");
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
        this.open(eve.target as Element, eve.clientX, eve.clientY);
    }

    public open(target: Element, x: number, y: number): void {
        if (this.state.open) {
            this.close();
        }

        this.state.open = true;
        this.state.target = target;
        this.state.x = x;
        this.state.y = y;
    }

    public close(): void {
        this.state.open = false;
    }
}