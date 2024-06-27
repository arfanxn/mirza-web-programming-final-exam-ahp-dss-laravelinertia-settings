<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import {
        goal,
        pwcsVectors,
        pwcsVectorsTotal,
        pwcsWeights,
        pwcsWeightsTotal,
        pwcsEigenValues,
        pwcsEigenValuesTotal,
    } from "../../stores/goalStore.js";
</script>

<!-- Criteria Vector, Weight, Eigen Value -->
<Card title="Criteria Vector, Weight, and Eigen Value" class="basis-8/12">
    <Matrix dimension={[$goal.criteria.length + 1, 4]}>
        <!-- Title matirix cell -->
        <MatrixCell
            disabled
            type="text"
            value={`Criteria / Utils`}
            row={1}
            column={1}
        />
        <MatrixCell disabled type="text" value={`Vector`} row={1} column={2} />
        <MatrixCell disabled type="text" value={`Weight`} row={1} column={3} />
        <MatrixCell
            disabled
            type="text"
            value={`Eigen Value`}
            row={1}
            column={4}
        />
        <!-- Total matrix cell -->
        <MatrixCell
            disabled
            type="text"
            value={`Total`}
            row={$goal.criteria.length + 2}
            column={1}
        />
        {#each $goal.criteria as criterion}
            <!-- Row name matrix cell -->
            <MatrixCell
                type="text"
                bind:value={criterion.name}
                row={criterion.order + 1}
                column={1}
            />
        {/each}
        {#each $pwcsVectors as value, row}
            <!-- Vectors cell -->
            <MatrixCell disabled {value} row={row + 2} column={2} />
        {/each}
        <MatrixCell
            disabled
            value={$pwcsVectorsTotal}
            row={$goal.criteria.length + 2}
            column={2}
        />
        {#each $pwcsWeights as value, row}
            <!-- Weights cell -->
            <MatrixCell disabled {value} row={row + 2} column={3} />
        {/each}
        <MatrixCell
            disabled
            value={$pwcsWeightsTotal}
            row={$goal.criteria.length + 2}
            column={3}
        />
        {#each $pwcsEigenValues as value, row}
            <!-- EV cell -->
            <MatrixCell disabled {value} row={row + 2} column={4} />
        {/each}
        <MatrixCell
            disabled
            value={$pwcsEigenValuesTotal}
            row={$goal.criteria.length + 2}
            column={4}
        />
    </Matrix>
</Card>
