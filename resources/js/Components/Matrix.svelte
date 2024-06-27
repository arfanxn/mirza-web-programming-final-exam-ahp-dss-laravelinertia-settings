<script>
    export let dimension;
</script>

<main
    class={`grid auto-cols-fr auto-rows-fr grid-rows-${dimension[0]} grid-cols-${dimension[1]} border border-white bg-blue-500 ${$$restProps.class}`}
>
    <slot></slot>
</main>
