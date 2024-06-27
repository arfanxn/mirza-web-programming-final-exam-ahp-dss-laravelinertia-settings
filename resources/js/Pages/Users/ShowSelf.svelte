<script>
    import MainLayout from "../../Layouts/MainLayout.svelte";
    import Card from "../../Components/Card.svelte";
    import SideMenu from "./SelfSideMenu.svelte";
    export let user;
</script>

<svelte:head>
    <title>{"Account Information"}</title>
</svelte:head>

<MainLayout>
    <div class="flex w-full items-start justify-between gap-8">
        <Card title="Account Information" class="grow">
            <ul class="font-semibold text-white">
                <li><p>Name : {user.name}</p></li>
                <li><p>Email : {user.email}</p></li>
            </ul>
        </Card>
        <SideMenu />
    </div>
</MainLayout>
