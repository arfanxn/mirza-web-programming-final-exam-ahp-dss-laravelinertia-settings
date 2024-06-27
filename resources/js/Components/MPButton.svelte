<script>
    /**
     * The component name is MPButton which stands for Minus Plus Button
     */
    import debounce from "lodash.debounce";

    export let value = 0; // default is zero
    export let onPlus;
    export let onMinus;
</script>

<main class="flex rounded-lg">
    <button
        on:click={debounce(
            () => {
                value--;
                onMinus();
            },
            200,
            { leading: true },
        )}
        class="text-md border-e-0.5 basis-1/2 rounded-s-lg border border-e-white bg-blue-500 px-2 py-1 font-semibold text-white outline-none transition-all active:ring-2 active:ring-blue-800 disabled:bg-blue-500/50 disabled:active:ring-0"
        >-
    </button>
    <button
        on:click={debounce(
            () => {
                value++;
                onPlus();
            },
            200,
            { leading: true },
        )}
        class="text-md border-s-0.5 basis-1/2 rounded-e-lg border border-s-white bg-blue-500 px-2 py-1 font-semibold text-white outline-none transition-all active:ring-2 active:ring-blue-800 disabled:bg-blue-500/50 disabled:active:ring-0"
        >+
    </button>
</main>
