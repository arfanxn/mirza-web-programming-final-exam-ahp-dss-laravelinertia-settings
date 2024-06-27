<script>
    export let row;
    export let type = "number";
    export let column;
    export let value;
    export let disabled = false;
    export let onInput = null;

    $: inputClass = `input-no-spinner row-start-${row} col-start-${column} ${$$restProps.class} border border-white bg-blue-500 px-2 py-1 text-center text-sm font-semibold outline-none transition-all focus:z-50 focus:bg-white focus:text-black focus:ring-8 focus:ring-pink-500`;

    function handleInput(e) {
        if (typeof onInput == "function") onInput(e);
    }
</script>

{#if type == "number"}
    <input
        type="number"
        {disabled}
        on:input={handleInput}
        class={`${inputClass}`}
        bind:value
    />
{:else if type == "text"}
    <input
        type="text"
        {disabled}
        on:input={handleInput}
        class={`${inputClass}`}
        bind:value
    />
{/if}
