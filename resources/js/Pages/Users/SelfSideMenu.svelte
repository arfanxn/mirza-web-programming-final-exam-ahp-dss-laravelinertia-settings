<script>
    import Card from "../../Components/Card.svelte";
    import { router } from "@inertiajs/svelte";

    const logoutRequest = () => {
        if (confirm("Are you sure you want to logout?")) {
            router.delete("/logout");
        }
    };
</script>

<Card class="grow-0 basis-3/12">
    <ul class="flex flex-col gap-2 text-lg font-semibold">
        <li>
            <a href="/users/self" class="underline">Account Information</a>
        </li>
        <li>
            <a href="/users/self/edit" class="underline">Account Settings</a>
        </li>
        <li>
            <button class="text-pink-500 underline" on:click={logoutRequest}
                >Logout
            </button>
        </li>
    </ul>
</Card>
