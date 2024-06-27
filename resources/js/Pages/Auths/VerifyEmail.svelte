<script>
    import MainLayout from "../../Layouts/MainLayout.svelte";
    import Card from "../../Components/Card.svelte";
    import { router } from "@inertiajs/svelte";

    export let auth;

    function resentVerificationRequest() {
        if (confirm("Resent email verification notification?")) {
            router.post("/users/self/email/verification-notification");
        }
    }
</script>

<svelte:head>
    <title>{"Verify Email"}</title>
</svelte:head>

<MainLayout>
    <Card
        title="Email Verification"
        class="absolute inset-0 m-auto h-fit w-4/12 font-semibold"
    >
        <p>
            The email verification link has been sent to
            <span class="italic">"{auth.email}"</span> or click
            <button
                type="button"
                class="text-blue-500 underline"
                on:click|preventDefault={resentVerificationRequest}
                >resend</button
            > to resend your email verification link if you didn't see one.
        </p>
    </Card>
</MainLayout>
