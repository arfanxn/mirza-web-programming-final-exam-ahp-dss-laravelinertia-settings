<script>
    import Card from "../Card.svelte";
    import TextField from "../TextField.svelte";
    import Textarea from "../Textarea.svelte";
    import { goal } from "../../stores/goalStore.js";
</script>

<Card title="Goal">
    <ul class="grow font-semibold text-white">
        <li class="flex min-h-14 items-center gap-2">
            <span class="basis-2/12 text-xl">Title</span>
            <TextField
                class="border-0 bg-transparent text-xl"
                bind:value={$goal.title}
            />
        </li>
        <li class="flex items-start gap-2">
            <span class="basis-2/12 text-lg">Description:</span>
            <Textarea
                bind:value={$goal.description}
                class="-mt-1 h-auto min-h-24 resize-y border-0 bg-transparent text-lg"
            />
        </li>
    </ul>
</Card>
