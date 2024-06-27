<script>
    export let title;
    export let contentClass = null;

    $: contentClass;
</script>

<main
    class="flex flex-col gap-2 rounded-lg border border-white bg-amber-500 p-4 text-white {$$restProps.class}"
>
    {#if !!title}
        <header class="">
            <h1 class="text-2xl font-semibold">{title}</h1>
        </header>
        <slot name="header"></slot>
        <hr class="w-full border-b border-white" />
    {/if}

    <section class={contentClass}>
        <slot />
    </section>

    <footer>
        <!-- TODO: create footer -->
    </footer>
</main>
