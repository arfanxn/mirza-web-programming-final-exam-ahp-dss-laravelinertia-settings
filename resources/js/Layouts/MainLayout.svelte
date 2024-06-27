<script>
    import Navbar from "./Navbar.svelte";
</script>

<main
    class="flex h-full min-h-screen w-full flex-col gap-8 bg-gradient-to-r from-amber-500/35 to-blue-500/35"
>
    <Navbar class="px-16" />

    <section class="px-16">
        <slot />
    </section>

    <footer>
        <!-- TODO: create footer -->
    </footer>
</main>
