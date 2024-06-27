<script>
    export let checked;
    export let onChecked = null;
    export let label = null;

    $: checked,
        () => {
            if (typeof onChecked != "function") return;
            onChecked();
        };
</script>

<button
    type="button"
    class={`text-md flex items-center gap-1 rounded-lg border border-white bg-blue-500 ${!!label ? "px-2" : "px-1"} py-1 font-semibold text-white transition-all active:ring-2 active:ring-blue-800 disabled:bg-blue-500/50 disabled:active:ring-0 ${$$restProps.class}`}
    on:click={() => (checked = !checked)}
>
    {#if !!label}
        <span>{label}</span>
    {/if}
    <input type="checkbox" bind:checked class="text-white accent-pink-500" />
</button>
