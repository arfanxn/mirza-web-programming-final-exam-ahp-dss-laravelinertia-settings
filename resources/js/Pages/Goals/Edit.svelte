<script>
    import MainLayout from "../../Layouts/MainLayout.svelte";
    import PssMatrixCard from "../../Components/Goals/PssMatrixCard.svelte";
    import Button from "../../Components/Button.svelte";
    import Alert from "../../Components/Alert.svelte";
    import GoalCard from "../../Components/Goals/GoalCard.svelte";
    import { page, router } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import { goal as goalStore } from "../../stores/goalStore.js";
    import PwcsMatrixCard from "../../Components/Goals/PwcsMatrixCard.svelte";
    import PwcsNormMatrixCard from "../../Components/Goals/PwcsNormMatrixCard.svelte";
    import AlternativeListCard from "../../Components/Goals/AlternativeListCard.svelte";
    import CriterionListCard from "../../Components/Goals/CriterionListCard.svelte";
    import PwcsEigenVectorCard from "../../Components/Goals/PwcsEigenVectorCard.svelte";
    import PwcsConsistencyCard from "../../Components/Goals/PwcsConsistencyCard.svelte";
    import PwcsPriorityVectorCards from "../../Components/Goals/PwcsPriorityVectorCards.svelte";
    import Rankingization from "../../Components/Goals/Rankingization.svelte";

    export let goal;
    $: goal, goalStore.set(goal);

    onMount(() => {
        goalStore.subscribe((g) => {
            console.log(g);
        });
    });

    function updateRequest() {
        router.put(`/goals/${$goalStore.id}`, $goalStore);
    }
</script>

<svelte:head>
    <title>{"Goal"}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</svelte:head>

<MainLayout>
    <main class="flex flex-col justify-between gap-8">
        <div class="flex flex-row-reverse items-end justify-between gap-8">
            <Button class="basis-48 py-1" on:click={updateRequest}
                ><span class="text-xl">Save</span></Button
            >
            <Alert class=" w-fit  bg-green-500" message={$page.props.message} />
        </div>

        <!-- Goal Information -->
        <GoalCard />

        <!-- Additonals Information (Criteria & Alternatives) -->
        <div class="flex basis-full gap-8">
            <CriterionListCard class="basis-7/12" />
            <AlternativeListCard class="basis-5/12" />
        </div>

        <!-- Performance Scores -->
        <PssMatrixCard />

        <!-- Pairwise Comparisons -->
        <PwcsMatrixCard />

        <!-- Normalized Pairwise Comparisons -->
        <PwcsNormMatrixCard />

        <div class="flex basis-full items-start gap-8">
            <PwcsEigenVectorCard />
            <PwcsConsistencyCard />
        </div>

        <PwcsPriorityVectorCards />

        <Rankingization />

        <div class="flex flex-row-reverse">
            <Button class="basis-48 py-1" on:click={updateRequest}
                ><span class="text-xl">Save</span></Button
            >
        </div>
    </main>
</MainLayout>
