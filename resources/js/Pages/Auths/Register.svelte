<script>
    // import Form from "../../Components/Form.svelte";
    import TextField from "../../Components/TextField.svelte";
    import Button from "../../Components/Button.svelte";
    import Alert from "../../Components/Alert.svelte";
    import Card from "../../Components/Card.svelte";
    import { useForm } from "@inertiajs/svelte";
    import GuestLayout from "../../Layouts/GuestLayout.svelte";

    let form = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    const registerRequest = () => {
        $form.post("/register");
    };
</script>

<svelte:head>
    <title>{"Register"}</title>
</svelte:head>

<GuestLayout>
    <Card title="Register" class="absolute inset-0 m-auto h-fit w-4/12">
        <form
            class="flex flex-col gap-2"
            on:submit|preventDefault={registerRequest}
        >
            <div class="flex flex-col gap-1">
                <TextField
                    label="Name"
                    type="text"
                    name="name"
                    placeholder="John Doe"
                    bind:value={$form.name}
                />
                <Alert class="bg-red-500" bind:message={$form.errors.name} />
            </div>
            <div class="flex flex-col gap-1">
                <TextField
                    label="Email"
                    type="text"
                    name="email"
                    placeholder="john@example.com"
                    bind:value={$form.email}
                />
                <Alert class="bg-red-500" bind:message={$form.errors.email} />
            </div>
            <div class="flex flex-col gap-1">
                <TextField
                    label="Password"
                    type="password"
                    name="password"
                    placeholder="********"
                    bind:value={$form.password}
                />
                <Alert
                    class="bg-red-500"
                    bind:message={$form.errors.password}
                />
            </div>
            <div class="flex flex-col gap-1">
                <TextField
                    label="Password Confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="********"
                    bind:value={$form.password_confirmation}
                />
                <Alert
                    class="bg-red-500"
                    bind:message={$form.errors.password_confirmation}
                />
            </div>

            <div class="flex items-start justify-between">
                <a
                    class="text-md grow-0 font-semibold text-white underline"
                    href="/login">Login</a
                >
                <Button type="submit" class="grow-0">Register</Button>
            </div>
        </form>
    </Card>
</GuestLayout>
