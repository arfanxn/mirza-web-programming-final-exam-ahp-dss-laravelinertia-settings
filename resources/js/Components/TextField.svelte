<script>
    export let disabled = false;
    export let label = null;
    export let value;
    export let name = null;
    export let type = "text";
    export let placeholder = null;
    export let labelPosition = "top";
    export let labelClass = null;
    export let onEnterKeypress = null;

    $: defaultLabelClass = `text-md block font-semibold text-white ${labelClass}`;
    let inputClass = `w-full rounded-lg border border-white bg-blue-500 px-2 py-1 font-semibold text-white outline-none placeholder:text-white/75 hover:border hover:border-white hover:bg-blue-500 hover:text-white active:border active:border-white active:bg-blue-500 active:text-white`;

    function handleKeypress(e) {
        if (e.charCode === 13 && typeof onEnterKeypress == "function")
            onEnterKeypress(e);
    }
</script>

<main
    class={`flex w-full ${labelPosition == "top" ? "flex-col items-start" : "flex-row items-center"} gap-x-1 gap-y-0.5`}
>
    {#if !!label}
        <label for="first_name" class={`${defaultLabelClass}`}>{label}</label>
    {/if}
    {#if type == "text"}
        <input
            {disabled}
            class={`${inputClass} ${$$restProps.class}`}
            type="text"
            {name}
            {placeholder}
            on:keypress={handleKeypress}
            bind:value
        />
    {:else if type == "password"}
        <input
            {disabled}
            class={`${inputClass} ${$$restProps.class}`}
            type="password"
            {name}
            {placeholder}
            bind:value
        />
    {/if}
</main>
