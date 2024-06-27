<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import {
        goal,
        normalizedPwcsMatrix,
        normalizedPwcsColumnsTotals,
    } from "../../stores/goalStore.js";
</script>

<Card title="Normalized Pairwise Comparisons">
    <Matrix dimension={[$goal.criteria.length + 2, $goal.criteria.length + 1]}>
        <!-- Title matrix cell -->
        <MatrixCell
            disabled
            type="text"
            value={`Criteria`}
            row={1}
            column={1}
        />
        <MatrixCell
            disabled
            type="text"
            value={`Total`}
            row={$goal.criteria.length + 2}
            column={1}
        />
        {#each $goal.criteria as criterion, index}
            <!-- Column name matrix cell -->
            <MatrixCell
                type="text"
                bind:value={criterion.name}
                row={1}
                column={criterion.index + 2}
            />
            <!-- Row name matrix cell -->
            <MatrixCell
                type="text"
                bind:value={criterion.name}
                row={criterion.index + 2}
                column={1}
            />
            <!-- Total of each matrix cell -->
            <MatrixCell
                type="number"
                value={$normalizedPwcsColumnsTotals[index]}
                column={criterion.index + 2}
                row={$goal.criteria.length + 2}
            />
        {/each}
        {#each $normalizedPwcsMatrix as cells, i}
            {#each cells as cell, j}
                <MatrixCell disabled value={cell} row={i + 2} column={j + 2}
                ></MatrixCell>
            {/each}
        {/each}
    </Matrix>
</Card>
