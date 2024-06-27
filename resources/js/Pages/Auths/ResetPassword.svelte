<script>
    import GuestLayout from "../../Layouts/GuestLayout.svelte";
    import TextField from "../../Components/TextField.svelte";
    import Alert from "../../Components/Alert.svelte";
    import Button from "../../Components/Button.svelte";
    import Card from "../../Components/Card.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";

    export let token;
    export let email;

    let form = useForm({
        token: token,
        email: email,
        password: "",
        password_confirmation: "",
    });

    const resetPasswordRequest = () => {
        $form.post("/reset-password/" + token);
    };
</script>

<svelte:head>
    <title>{"Reset Password"}</title>
</svelte:head>

<GuestLayout>
    <Card title="Reset Password" class="absolute inset-0 m-auto h-fit w-4/12">
        <form class="flex flex-col gap-2">
            <div class="flex flex-col gap-y-1">
                <TextField
                    disabled
                    label="Email"
                    name="email"
                    placeholder="john@example.com"
                    bind:value={$form.email}
                />
                <Alert class="bg-red-500" bind:message={$form.errors.email} />
            </div>
            <div class="flex flex-col gap-y-1">
                <TextField
                    type="password"
                    label="Password"
                    name="password"
                    placeholder="********"
                    bind:value={$form.password}
                />
                <Alert
                    class="bg-red-500"
                    bind:message={$form.errors.password}
                />
            </div>
            <div class="flex flex-col gap-y-1">
                <TextField
                    type="password"
                    label="Password Confirmation"
                    name="password"
                    placeholder="********"
                    bind:value={$form.password_confirmation}
                />
                <Alert
                    class="bg-red-500"
                    bind:message={$form.errors.password_confirmation}
                />
            </div>

            <Alert class="bg-green-500" message={$page.props?.message} />
            <div class="flex flex-row-reverse">
                <Button
                    type="button"
                    class="col-span-1"
                    on:click={resetPasswordRequest}>Continue</Button
                >
            </div>
        </form>
    </Card>
</GuestLayout>
