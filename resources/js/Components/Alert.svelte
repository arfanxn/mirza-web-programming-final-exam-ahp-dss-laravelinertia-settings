<script>
    import Fa from "svelte-fa";
    import { faX } from "@fortawesome/free-solid-svg-icons";

    export let message;
    export let onClose = null;
    $: isClosed = !!message == false;
</script>

<main
    class={`text-md flex items-center justify-between gap-4 rounded-lg px-2 py-1 font-semibold text-white transition-all ${isClosed ? "hidden" : ""} ${$$restProps.class}`}
>
    <span>{message ?? message}</span>
    <button
        on:click|preventDefault={() => {
            message = null; // remove message to hide element
            if (typeof onClose == "function") onClose();
        }}><Fa class="stroke-2" icon={faX} /></button
    >
</main>
