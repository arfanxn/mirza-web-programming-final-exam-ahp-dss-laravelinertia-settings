<script>
    import Card from "../Card.svelte";
    import Matrix from "../Matrix.svelte";
    import MatrixCell from "../MatrixCell.svelte";
    import { goal, rankingizationScores } from "../../stores/goalStore.js";
    import { rankOf } from "../../utilities/arrayUtils";
</script>

<!-- Rankingization Scores  -->
<Card title="Rankingization Scores">
    <Matrix dimension={[$goal.alternatives.length + 1, 3]}>
        <!-- Title matrix cell -->
        <MatrixCell
            disabled
            type="text"
            value={`Alternatives`}
            row={1}
            column={1}
        />
        <MatrixCell disabled type="text" value={`Scores`} row={1} column={2} />
        <MatrixCell disabled type="text" value={`Rank`} row={1} column={3} />

        {#each $goal.alternatives as alternative, index}
            <!-- Row name matrix cell -->
            <MatrixCell
                type="text"
                bind:value={alternative.name}
                row={alternative.index + 2}
                column={1}
            />
        {/each}
        {#each $rankingizationScores as score, criterionIndex}
            <MatrixCell
                disabled
                value={score}
                row={criterionIndex + 2}
                column={2}
            />
            <MatrixCell
                disabled
                value={rankOf(
                    $rankingizationScores[criterionIndex],
                    $rankingizationScores,
                )}
                row={criterionIndex + 2}
                column={3}
            />
        {/each}
    </Matrix>
</Card>
