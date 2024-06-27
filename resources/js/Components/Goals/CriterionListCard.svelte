<script>
    import Card from "../Card.svelte";
    import TextField from "../TextField.svelte";
    import MPButton from "../MPButton.svelte";
    import Toggle from "../Toggle.svelte";
    import {
        goal,
        addCriterion,
        subtractCriterion,
    } from "../../stores/goalStore.js";
</script>

<Card title="Criteria" class={`${$$restProps.class}`}>
    <ul class="flex flex-col gap-1 font-semibold text-white">
        {#each $goal.criteria as criterion}
            <li class="flex h-8 items-center justify-between">
                <TextField
                    class="text-md -ms-2 border-0 bg-transparent"
                    bind:value={criterion.name}
                />
                <div class="flex basis-4/12 flex-row-reverse gap-1">
                    <Toggle bind:checked={criterion.impact_type} />
                    <span class="text-md"
                        >{`(${criterion.impact_type ? "Benefit" : "Cost"})`}</span
                    >
                </div>
            </li>
        {/each}
        <!-- TODO: Fix criterion addition bug and error -->
        <!-- <li class="mt-1 flex flex-row-reverse items-center">
            <MPButton onPlus={addCriterion} onMinus={subtractCriterion}
            ></MPButton>
        </li> -->
    </ul>
</Card>
