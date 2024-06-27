<script>
    export let checked;
</script>

<label class="flex cursor-pointer flex-row items-center justify-between gap-1">
    <input type="checkbox" bind:checked class="peer sr-only" />
    <div
        class={`peer relative h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] active:ring-2 active:ring-blue-800 disabled:bg-blue-500/50 disabled:active:ring-0 peer-checked:bg-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white rtl:peer-checked:after:-translate-x-full ${$$restProps.class}`}
    ></div>
</label>
