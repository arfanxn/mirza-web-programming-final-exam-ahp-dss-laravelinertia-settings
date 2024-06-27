<script>
    import MainLayout from "../../Layouts/MainLayout.svelte";
    import TextField from "../../Components/TextField.svelte";
    import Alert from "../../Components/Alert.svelte";
    import Button from "../../Components/Button.svelte";
    import Card from "../../Components/Card.svelte";
    import Checkbox from "../../Components/Checkbox.svelte";
    import SideMenu from "./SelfSideMenu.svelte";
    import { router } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    export let user;

    let form = useForm({
        name: user.name,
        email: user.email,
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
    });

    function clearPasswordFields() {
        $form.current_password = "";
        $form.new_password = "";
        $form.new_password_confirmation = "";
    }
    function updateRequest() {
        $form.put("/users/" + user.id);
    }

    $: arePasswordFieldsShowed = false;
    $: if (arePasswordFieldsShowed == false) clearPasswordFields();
</script>

<svelte:head>
    <title>{"Account Settings"}</title>
</svelte:head>

<MainLayout>
    <div class="flex w-full items-start justify-between gap-8">
        <Card title="Account Settings" class="grow">
            <form
                class="flex flex-col gap-2"
                on:submit|preventDefault={updateRequest}
            >
                <div class="flex flex-col gap-1">
                    <TextField
                        label="Name"
                        type="text"
                        name="name"
                        placeholder="John Doe"
                        bind:value={$form.name}
                    />
                    <Alert
                        class="bg-red-500"
                        bind:message={$form.errors.name}
                    />
                </div>
                <div class="flex flex-col gap-1">
                    <TextField
                        label="Email"
                        type="text"
                        name="email"
                        placeholder="john@example.com"
                        bind:value={$form.email}
                    />
                    <Alert
                        class=" bg-red-500"
                        bind:message={$form.errors.email}
                    />
                </div>
                {#if arePasswordFieldsShowed}
                    <div class="translate-all flex flex-col gap-1">
                        <TextField
                            label="Current Password"
                            type="password"
                            name="current_password"
                            placeholder="********"
                            bind:value={$form.current_password}
                        />
                        <Alert
                            class="bg-red-500"
                            bind:message={$form.errors.current_password}
                        />
                    </div>
                    <div class="translate-all flex flex-col gap-1">
                        <TextField
                            label="New Password"
                            type="password"
                            name="new_password"
                            placeholder="********"
                            bind:value={$form.new_password}
                        />

                        <Alert
                            class="bg-red-500"
                            bind:message={$form.errors.new_password}
                        />
                    </div>
                    <div class="translate-all flex flex-col gap-1">
                        <TextField
                            label="New Password Confirmation"
                            type="password"
                            name="new_password_confirmation"
                            placeholder="********"
                            bind:value={$form.new_password_confirmation}
                        />

                        <Alert
                            class="bg-red-500"
                            bind:message={$form.errors
                                .new_password_confirmation}
                        />
                    </div>
                {/if}
                <div class="flex flex-row-reverse items-center gap-2">
                    <Button
                        class="grow-0"
                        type="button"
                        on:click={updateRequest}>Update</Button
                    >
                    <Checkbox
                        label="Edit password"
                        bind:checked={arePasswordFieldsShowed}
                    />
                    <Alert
                        class="grow bg-green-500"
                        message={$page.props?.message}
                    />
                </div>
            </form>
        </Card>
        <SideMenu />
    </div>
</MainLayout>
