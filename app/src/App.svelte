<script>
    import "./app.scss"
    import { onMount } from "svelte"
    import { config } from "./Store/ConfigStore"
    import { state } from "./Store/StateStore"
    import { ApiService } from "./Service/ApiService"
    import SyncFeed from "./Action/SyncFeed.svelte"
    import ResetFeed from "./Action/ResetFeed.svelte"

    // vars
    let action
    let feed

    // reactives
    $:currentAction = action
    $:currentFeed = feed

    // wordpress api
    const api = ApiService( $config.siteUrl, $config.nonce )


    // fns
    const onAirtable = async e => {
        state.reset()
        action = e.detail.action

        // get feed
        const res = await api.feed( e.detail.feed_id )
        const json = await res.json()
        feed = json.feed
        console.log( { feed } )
    }

    onMount( () => {
        $config
        console.log( { $config } )
        window.addEventListener( 'airtable', onAirtable )
    } )

</script>


{#if feed}
    {#if currentAction === 'sync-feed'}
        <SyncFeed {feed}/>

    {:else if (currentAction === 'reset-feed')}
        <ResetFeed {feed}/>
    {/if}
{/if}
