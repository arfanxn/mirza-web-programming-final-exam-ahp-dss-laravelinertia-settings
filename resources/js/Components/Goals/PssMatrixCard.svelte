<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import { goal } from "../../stores/goalStore.js";
</script>

<Card title="Performance scores">
    <Matrix
        dimension={[$goal.alternatives.length + 1, $goal.criteria.length + 1]}
    >
        <MatrixCell
            type="text"
            disabled
            value="Alternatives / Criteria"
            row={1}
            column={1}
        />
        {#each $goal.criteria as criterion}
            <MatrixCell
                type="text"
                bind:value={criterion.name}
                row={1}
                column={criterion.index + 2}
            />
        {/each}
        {#each $goal.alternatives as alternative}
            <MatrixCell
                type="text"
                bind:value={alternative.name}
                row={alternative.index + 2}
                column={1}
            />
        {/each}
        {#each $goal.performance_scores as performanceScore, index}
            <MatrixCell
                type="number"
                bind:value={performanceScore.value}
                row={performanceScore.alternative.index + 2}
                column={performanceScore.criterion.index + 2}
            />
        {/each}
    </Matrix>
</Card>
