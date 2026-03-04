"use strict";

import { reactive, ref, nextTick, onMounted, onBeforeUnmount, h, Teleport } from "https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js";

/**
 * Item-Schema:
 * {
 *   label: string,
 *   icon?: string | (() => VNode),   // z.B. emoji / text oder render-fn
 *   disabled?: boolean,
 *   shortcut?: string,
 *   onClick?: (payload) => void
 * }
 */
export function createContextMenu() {
    const state = reactive({
        open: false,
        x: 0,
        y: 0,
        items: [],
        payload: null,
        active: -1,
    });

    const menuEl = ref(null);

    function firstEnabledIndex(items) {
        return items.findIndex((it) => !it.disabled);
    }

    function clampToViewport() {
        const el = menuEl.value;
        if (!el) return;

        const rect = el.getBoundingClientRect();
        const pad = 8;

        const maxX = window.innerWidth - rect.width - pad;
        const maxY = window.innerHeight - rect.height - pad;

        state.x = Math.max(pad, Math.min(state.x, maxX));
        state.y = Math.max(pad, Math.min(state.y, maxY));
    }

    async function open(evt, items, payload = null) {
        evt?.preventDefault?.();

        state.items = Array.isArray(items) ? items : [];
        state.payload = payload;
        state.open = true;

        state.x = evt?.clientX ?? 0;
        state.y = evt?.clientY ?? 0;

        state.active = firstEnabledIndex(state.items);

        await nextTick();
        clampToViewport();

        // Fokus auf Menü, damit Keyboard funktioniert
        menuEl.value?.focus?.();
    }

    function close() {
        state.open = false;
        state.items = [];
        state.payload = null;
        state.active = -1;
    }

    function clickItem(item) {
        if (!item || item.disabled) return;
        try {
            item.onClick?.(state.payload);
        } finally {
            close();
        }
    }

    function moveActive(dir) {
        if (!state.items.length) return;

        let i = state.active;
        for (let step = 0; step < state.items.length; step++) {
            i = (i + dir + state.items.length) % state.items.length;
            if (!state.items[i].disabled) {
                state.active = i;
                return;
            }
        }
    }

    function onKeydown(e) {
        if (!state.open) return;

        if (e.key === "Escape") {
            e.preventDefault();
            close();
        } else if (e.key === "ArrowDown") {
            e.preventDefault();
            moveActive(+1);
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            moveActive(-1);
        } else if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            clickItem(state.items[state.active]);
        }
    }

    function onGlobalPointerDown(e) {
        if (!state.open) return;
        const el = menuEl.value;
        if (el && !el.contains(e.target)) close();
    }

    function onGlobalScrollOrResize() {
        if (state.open) clampToViewport();
    }

    const ContextMenu = {
        name: "ContextMenu",
        setup() {
            onMounted(() => {
                window.addEventListener("pointerdown", onGlobalPointerDown, true);
                window.addEventListener("keydown", onKeydown, true);
                window.addEventListener("resize", onGlobalScrollOrResize, { passive: true });
                window.addEventListener("scroll", onGlobalScrollOrResize, { passive: true, capture: true });
            });
            onBeforeUnmount(() => {
                window.removeEventListener("pointerdown", onGlobalPointerDown, true);
                window.removeEventListener("keydown", onKeydown, true);
                window.removeEventListener("resize", onGlobalScrollOrResize);
                window.removeEventListener("scroll", onGlobalScrollOrResize, true);
            });

            return () =>
                state.open
                    ? h(
                        Teleport,
                        { to: "body" },
                        h(
                            "div",
                            {
                                ref: menuEl,
                                tabindex: -1,
                                role: "menu",
                                style: {
                                    position: "fixed",
                                    left: `${state.x}px`,
                                    top: `${state.y}px`,
                                    zIndex: 999999,
                                    minWidth: "220px",
                                    padding: "6px",
                                    borderRadius: "10px",
                                    background: "rgba(24, 24, 28, 0.98)",
                                    color: "white",
                                    border: "1px solid rgba(255,255,255,0.08)",
                                    boxShadow: "0 12px 36px rgba(0,0,0,0.45)",
                                    outline: "none",
                                    font: "14px/1.3 system-ui, -apple-system, Segoe UI, Roboto, sans-serif",
                                },
                            },
                            state.items.map((item, idx) =>
                                h(
                                    "button",
                                    {
                                        type: "button",
                                        role: "menuitem",
                                        disabled: !!item.disabled,
                                        onPointerenter: () => {
                                            if (!item.disabled) state.active = idx;
                                        },
                                        onClick: () => clickItem(item),
                                        style: {
                                            width: "100%",
                                            display: "grid",
                                            gridTemplateColumns: "20px 1fr auto",
                                            gap: "10px",
                                            alignItems: "center",
                                            padding: "8px 10px",
                                            borderRadius: "8px",
                                            border: "0",
                                            background:
                                                idx === state.active ? "rgba(255,255,255,0.12)" : "transparent",
                                            color: item.disabled ? "rgba(255,255,255,0.35)" : "white",
                                            cursor: item.disabled ? "not-allowed" : "pointer",
                                            textAlign: "left",
                                        },
                                    },
                                    [
                                        typeof item.icon === "function"
                                            ? item.icon()
                                            : h("span", { style: { opacity: 0.9 } }, item.icon ?? ""),
                                        h("span", null, item.label),
                                        h(
                                            "span",
                                            { style: { opacity: 0.55, fontSize: "12px" } },
                                            item.shortcut ?? ""
                                        ),
                                    ]
                                )
                            )
                        )
                    )
                    : null;
        },
    };

    return { state, open, close, ContextMenu };
}