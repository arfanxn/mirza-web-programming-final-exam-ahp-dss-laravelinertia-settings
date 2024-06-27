<script>
    export let disabled = false;
    export let label = null;
    export let value;
    export let name = null;
    export let placeholder = null;
</script>

<div class="flex w-full flex-col gap-y-0.5">
    {#if !!label}
        <label for="first_name" class="text-md block font-semibold text-white"
            >{label}</label
        >
    {/if}
    <textarea
        {disabled}
        class={`w-full scroll-m-0 scroll-p-0 overflow-auto rounded-lg border border-white bg-blue-500 px-2 py-1 text-white outline-none placeholder:text-white/75 hover:border hover:border-white hover:bg-blue-500 hover:text-white active:border active:border-white active:bg-blue-500 active:text-white ${$$restProps.class}`}
        {name}
        {placeholder}
        bind:value
    ></textarea>
</div>
