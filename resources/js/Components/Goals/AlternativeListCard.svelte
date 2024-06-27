<script>
    import Card from "../Card.svelte";
    import TextField from "../TextField.svelte";
    import MPButton from "../MPButton.svelte";
    import {
        goal,
        addAlternative,
        subtractAlternative,
    } from "../../stores/goalStore.js";
</script>

<Card title="Alternatives" class={`${$$restProps.class}`}>
    <ul class="flex grow flex-col gap-1 font-semibold text-white">
        {#each $goal.alternatives as alternative}
            <li class="h-8">
                <TextField
                    class="text-md -ms-2 w-full border-0 bg-transparent"
                    bind:value={alternative.name}
                />
            </li>
        {/each}
        <li class="mt-1 flex flex-row-reverse items-center">
            <MPButton onPlus={addAlternative} onMinus={subtractAlternative}
            ></MPButton>
        </li>
    </ul>
</Card>
