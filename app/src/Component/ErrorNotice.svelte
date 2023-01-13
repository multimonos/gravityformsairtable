<script>
    export let message
    export let dismissable = true
</script>
<div class="notice notice-error is-dismissible" class:is-dismissible={dismissable}>
    <p><strong>Ooops! Something went wrong ...</strong></p>
    {#if message}
        <p>{@html message}</p>
    {/if}
</div>