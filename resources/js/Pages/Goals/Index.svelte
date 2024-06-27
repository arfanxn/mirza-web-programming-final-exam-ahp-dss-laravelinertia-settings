<script>
    import MainLayout from "../../Layouts/MainLayout.svelte";
    import Card from "../../Components/Card.svelte";
    import Alert from "../../Components/Alert.svelte";
    import TextField from "../../Components/TextField.svelte";
    import Button from "../../Components/Button.svelte";
    import Fa from "svelte-fa";
    import {
        faMagnifyingGlass,
        faPenToSquare,
        faPlus,
        faTrash,
    } from "@fortawesome/free-solid-svg-icons";
    import { router, page } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import { urlSearchParams } from "../../helpers/urlHelpers.js";

    export let goals;

    const urlParams = {
        page: urlSearchParams("page"),
        keyword: urlSearchParams("keyword"),
    };
    function searchRequest() {
        router.get("/goals", urlParams);
    }
    function addRequest() {
        router.post("/goals", urlParams);
    }
    function editRequest(id) {
        router.visit("/goals/" + id);
    }
    function deleteRequest(id) {
        if (confirm("Are you sure you want to delete?")) {
            router.delete("/goals/" + id);
        }
    }

    $: if (urlParams?.keyword?.length == 0) {
        searchRequest();
    }

    onMount(() => {});
</script>

<svelte:head>
    <title>{"Dashboard"}</title>
</svelte:head>

<MainLayout>
    <main class="flex flex-col justify-between gap-8">
        <Alert class=" w-fit  bg-green-500" message={$page.props.message} />

        <Card title="Goals">
            <main class="flex flex-col gap-2">
                <form
                    on:submit|preventDefault
                    class="flex justify-between gap-x-2"
                >
                    <div class="flex basis-full gap-x-2">
                        <TextField
                            onEnterKeypress={searchRequest}
                            bind:value={urlParams.keyword}
                            placeholder="Search for goal . . ."
                        />
                        <Button on:click={searchRequest}
                            ><Fa class="" icon={faMagnifyingGlass} /></Button
                        >
                    </div>

                    <Button class="block" on:click={addRequest}
                        ><Fa class="" icon={faPlus} /></Button
                    >
                </form>

                <table
                    class="table-auto border-separate border-spacing-4 rounded-lg border border-white bg-blue-500 font-semibold"
                >
                    <thead>
                        <tr class="border">
                            <th class="">Title</th>
                            <th class="">Description</th>
                            <th class="">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each goals.data as goal}
                            <tr>
                                <td class="">{goal.title}</td>
                                <td class="">{goal.description ?? "-"}</td>
                                <td class="flex justify-center gap-4">
                                    <button
                                        on:click={() => editRequest(goal.id)}
                                        ><Fa class="" icon={faPenToSquare} />
                                    </button>
                                    <button
                                        href="/goals/{goal.id}"
                                        on:click={() => deleteRequest(goal.id)}
                                        ><Fa class="" icon={faTrash} />
                                    </button>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>

                <footer class="flex w-full justify-between gap-2">
                    <Button
                        disabled={goals.first_page_url == null}
                        href={goals.first_page_url}
                        class="grow-0">First</Button
                    >
                    <div class="flex gap-2">
                        <Button
                            disabled={goals.prev_page_url == null}
                            href={goals.prev_page_url}>Prev</Button
                        >
                        <Button
                            disabled={goals.next_page_url == null}
                            href={goals.next_page_url}>Next</Button
                        >
                    </div>
                </footer>
            </main>
        </Card>
    </main>
</MainLayout>
