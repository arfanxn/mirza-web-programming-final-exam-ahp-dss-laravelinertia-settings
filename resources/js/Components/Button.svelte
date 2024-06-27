<script>
    import { router } from "@inertiajs/svelte";

    export let type = "button";
    export let href = undefined;
    export let disabled = false;

    function visit() {
        if (!href) return;
        router.visit(href);
    }

    function defaultEvents(node) {
        node.addEventListener("click", visit);

        return {
            destroy() {
                node.removeEventListener("click", visit);
            },
        };
    }
</script>

<button
    {type}
    {disabled}
    use:defaultEvents
    on:click
    on:mouseover
    on:mouseenter
    on:mouseleave
    on:focus
    class="text-md rounded-lg border border-white bg-blue-500 px-2 py-1 font-semibold text-white outline-none transition-all active:ring-2 active:ring-blue-800 disabled:bg-blue-500/50 disabled:active:ring-0 {$$restProps.class}"
>
    <slot />
</button>
