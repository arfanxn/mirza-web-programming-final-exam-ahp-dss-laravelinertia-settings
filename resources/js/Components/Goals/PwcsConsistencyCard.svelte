<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import {
        pwcsTStatistic,
        pwcsConfidenceInterval,
        pwcsRiskInterval,
        pwcsConsistencyRatio,
    } from "../../stores/goalStore.js";
</script>

<!-- t-Statistic, CI, RI, CR -->
<Card title="t, CI, RI, and CR" class="basis-4/12">
    <Matrix dimension={[2, 2]}>
        <!-- Title matirix cell -->
        <MatrixCell disabled type="text" value={`t`} row={1} column={1} />
        <MatrixCell
            disabled
            type="number"
            value={$pwcsTStatistic}
            row={1}
            column={2}
        />
        <MatrixCell disabled type="text" value={`CI`} row={2} column={1} />
        <MatrixCell
            disabled
            type="number"
            value={$pwcsConfidenceInterval}
            row={2}
            column={2}
        />
        <MatrixCell disabled type="text" value={`RI`} row={3} column={1} />
        <MatrixCell
            disabled
            type="number"
            value={$pwcsRiskInterval}
            row={3}
            column={2}
        />
        <MatrixCell disabled type="text" value={`CR`} row={4} column={1} />
        <MatrixCell
            disabled
            type="number"
            value={$pwcsConsistencyRatio}
            row={4}
            column={2}
        />
        <MatrixCell
            disabled
            class={`col-span-2 ${$pwcsConsistencyRatio <= 0.1 ? "bg-green-500" : "bg-pink-500"}`}
            type="text"
            value={$pwcsConsistencyRatio <= 0.1
                ? "Ratio is consistent"
                : "Ratio is inconsistent"}
            row={5}
        />
    </Matrix>
</Card>
