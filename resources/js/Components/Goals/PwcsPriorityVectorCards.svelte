<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import {
        goal,
        pssCriteriaAlternativesVectorsTotals,
        pssCriteriaAlternativesWeightsTotals,
        pssCriteriaAlternativesVectors,
        pssCriteriaAlternativesWeights,
    } from "../../stores/goalStore.js";
</script>

<main class="grid grid-cols-2 items-start gap-8">
    {#each $goal.criteria as criterion}
        <!-- Alternative Vector, and Weight -->
        <Card title={`Criterion: ${criterion.name}`}>
            <section>
                <Matrix dimension={[$goal.alternatives.length + 1, 3]}>
                    <!-- Title matirix cell -->
                    <MatrixCell
                        disabled
                        type="text"
                        value={`Alternatives / Utils`}
                        row={1}
                        column={1}
                    />
                    <MatrixCell
                        disabled
                        type="text"
                        value={`Vector`}
                        row={1}
                        column={2}
                    />
                    <MatrixCell
                        disabled
                        type="text"
                        value={`Weight`}
                        row={1}
                        column={3}
                    />
                    <!-- Total matrix cell -->
                    <MatrixCell
                        disabled
                        type="text"
                        value={`Total`}
                        row={$goal.alternatives.length + 2}
                        column={1}
                    />
                    <!-- Alternative vector total matrix cell -->
                    <MatrixCell
                        disabled
                        type="number"
                        value={$pssCriteriaAlternativesVectorsTotals[
                            criterion.index
                        ]}
                        row={$goal.alternatives.length + 2}
                        column={2}
                    />
                    <!-- Alternative weight total matrix cell -->
                    <MatrixCell
                        disabled
                        type="number"
                        value={$pssCriteriaAlternativesWeightsTotals[
                            criterion.index
                        ]}
                        row={$goal.alternatives.length + 2}
                        column={3}
                    />
                    {#each $goal.alternatives as alternative}
                        <!-- Alternative name matrix cell -->
                        <MatrixCell
                            type="text"
                            value={alternative.name}
                            row={alternative.index + 2}
                            column={1}
                        />
                        <!-- Alternative vector matrix cell -->
                        <MatrixCell
                            disabled
                            type="number"
                            value={$pssCriteriaAlternativesVectors[
                                criterion.index
                            ][alternative.index]}
                            row={alternative.index + 2}
                            column={2}
                        />
                        <!-- Alternative weight matrix cell -->
                        <MatrixCell
                            disabled
                            type="number"
                            value={$pssCriteriaAlternativesWeights[
                                criterion.index
                            ][alternative.index]}
                            row={alternative.index + 2}
                            column={3}
                        />
                    {/each}
                </Matrix>
            </section>
        </Card>
    {/each}
</main>
