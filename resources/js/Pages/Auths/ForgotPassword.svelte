<script>
    import GuestLayout from "../../Layouts/GuestLayout.svelte";
    import TextField from "../../Components/TextField.svelte";
    import Alert from "../../Components/Alert.svelte";
    import Button from "../../Components/Button.svelte";
    import Card from "../../Components/Card.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";

    let form = useForm({
        email: "",
    });

    const forgotPasswordRequest = () => {
        $form.post("/forgot-password");
    };
</script>

<svelte:head>
    <title>{"Forgot Password"}</title>
</svelte:head>

<GuestLayout>
    <Card title="Forgot Password" class="absolute inset-0 m-auto h-fit w-4/12">
        <form class="flex flex-col gap-2" on:submit|preventDefault>
            <div class="col-span-4 flex flex-col gap-1">
                <TextField
                    label="Email"
                    name="email"
                    placeholder="john@example.com"
                    bind:value={$form.email}
                />
                <Alert class="bg-red-500" bind:message={$form.errors.email} />
                <Alert class="bg-green-500" message={$page.props?.message} />
            </div>
            <div class="flex justify-between">
                <a
                    class="text-md grow-0 font-semibold text-white underline"
                    href="/login">Login</a
                >
                <Button class="grow-0" on:click={forgotPasswordRequest}
                    >Continue</Button
                >
            </div>
        </form>
    </Card>
</GuestLayout>
