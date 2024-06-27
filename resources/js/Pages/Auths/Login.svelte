<script>
    import TextField from "../../Components/TextField.svelte";
    import Button from "../../Components/Button.svelte";
    import Alert from "../../Components/Alert.svelte";
    import Card from "../../Components/Card.svelte";
    import { page } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";
    import GuestLayout from "../../Layouts/GuestLayout.svelte";

    let form = useForm({
        email: "",
        password: "",
    });

    const loginRequest = () => {
        $form.post("/login");
    };
</script>

<svelte:head>
    <title>{"Login"}</title>
</svelte:head>

<GuestLayout>
    <Card title="Login" class="absolute inset-0 m-auto h-fit w-4/12">
        <form
            class="flex flex-col gap-2"
            on:submit|preventDefault={loginRequest}
        >
            <div class="flex flex-col gap-1">
                <TextField
                    label="Email"
                    name="email"
                    placeholder="john@example.com"
                    bind:value={$form.email}
                />
                <Alert class="bg-red-500" bind:message={$form.errors.email} />
            </div>
            <div class="flex flex-col gap-1">
                <TextField
                    type="password"
                    label="Password"
                    name="password"
                    placeholder="********"
                    bind:value={$form.password}
                />
                <Alert
                    class=" bg-red-500"
                    bind:message={$form.errors.password}
                />
            </div>
            <Alert class="bg-green-500" message={$page.props?.message} />

            <div class="text-md flex justify-between text-white">
                <div class="basis-8/12">
                    <p class="break-all font-semibold">
                        <a class="col-span-2 underline" href="/register"
                            >Register</a
                        ><span>&nbsp;or login with&nbsp;</span><a
                            class="col-span-2 underline"
                            href="/login/google/redirect">Google</a
                        ><span>&nbsp;or&nbsp;</span><a
                            class="col-span-2 underline"
                            href="/forgot-password">Forgot password</a
                        >
                    </p>
                </div>

                <div class="grow-0">
                    <Button type="submit" class="">Login</Button>
                </div>
            </div>
        </form>
    </Card>
</GuestLayout>
