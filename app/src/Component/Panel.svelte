<script>
    import { state } from "../Store/StateStore"
    import { config } from "../Store/ConfigStore"
    import ErrorNotice from "./ErrorNotice.svelte"

    // props
    export let title
</script>
<div class="gform-settings-panel">

    <header class="gform-settings-panel__header">
        <h3 class="gform-settings-panel__title"><span>{title}</span></h3>
        <img src="{$config.avatarUrl}" alt="Airtable logo">
    </header>

    <div class="gform-settings-panel__content">
        <div class="inner-content">
            <br/>

            {#if $state.errorMessage}
                <ErrorNotice/>
                <br/>
            {/if}

            <slot></slot>

        </div>
    </div>

</div>