<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import { goal, rankingizationMatrix } from "../../stores/goalStore.js";
</script>

<Card title="Rankingization">
    <Matrix
        dimension={[$goal.alternatives.length + 1, $goal.criteria.length + 1]}
    >
        <!-- Title matrix cell -->
        <MatrixCell
            disabled
            type="text"
            value={`Alternatives / Criteria`}
            row={1}
            column={1}
        />
        {#each $goal.alternatives as alternative, index}
            <!-- Row name matrix cell -->
            <MatrixCell
                type="text"
                bind:value={alternative.name}
                row={alternative.index + 2}
                column={1}
            />
        {/each}
        {#each $goal.criteria as criterion}
            <!-- Column name matrix cell -->
            <MatrixCell
                type="text"
                bind:value={criterion.name}
                row={1}
                column={criterion.index + 2}
            />
            {#each $goal.alternatives as alternative}
                <MatrixCell
                    disabled
                    value={$rankingizationMatrix[criterion.index][
                        alternative.index
                    ]}
                    row={alternative.index + 2}
                    column={criterion.index + 2}
                />
            {/each}
        {/each}
    </Matrix>
</Card>
