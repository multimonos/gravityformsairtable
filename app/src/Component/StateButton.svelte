<script>
    import { state } from "../Store/StateStore"
    import { createEventDispatcher } from "svelte"

    // props
    export let startText = 'Start'
    export let stopText = 'Stop'

    // vars
    const dispatch = createEventDispatcher()

    // fns
    const start = e => dispatch( 'start' )
    const stop = e => dispatch( 'stop' )
</script>

<div class="state-button-wrapper">
    {#if ! $state.inProgress || $state.isComplete}
        <button on:click={start} class="button button-primary button-hero">{@html startText}</button>
    {:else}
        <button on:click={stop} class="button button-primary button-hero">{@html stopText}</button>
        <span class="spinner is-active"></span>
    {/if}
</div>