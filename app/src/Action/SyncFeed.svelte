<script>
    import { state } from "../Store/StateStore"
    import { config } from "../Store/ConfigStore"
    import { ApiService } from "../Service/ApiService"
    import StateButton from "../Component/StateButton.svelte"
    import ProgressBar from "../Component/ProgressBar.svelte"
    import Panel from "../Component/Panel.svelte"

    // props
    export let feed

    // vars
    let api = ApiService( $config.siteUrl, $config.nonce )

    // fns
    const syncFeed = async ( id ) => {

        const p = new Promise( ( resolve, reject ) => {

            const loop = async () => {

                // exit : halt by user
                if ( ! $state.inProgress ) {
                    return resolve( true )
                }

                // process
                const res = await api.feedSync( id )

                if ( ! res.ok ) {
                    reject( res )
                }

                const json = await res.json()

                // update state
                $state.processed = json.feed_sync_status.synced

                // exit : complete
                if ( json.feed_sync_status.total === json.feed_sync_status.synced ) {
                    $state.isComplete = true
                    return resolve( true )
                }

                // recurse
                loop()
            }

            // first run
            loop()
        } )

        return p
    }


    const stop = ( e ) => {
        $state.inProgress = false
    }

    const start = async ( e ) => {
        try {
            // don't double do
            if ( $state.inProgress ) return

            // reset state
            state.reset()
            $state.total = feed.sync_status.total
            $state.processed = feed.sync_status.synced
            $state.inProgress = true

            // sync
            await syncFeed( feed.id )
            $state.inProgress = false

        } catch ( e ) {
            $state.errorMessage = `${ e.status } : ${ e.statusText }`
            $state.inProgress = false
            console.error( 'error', e )
        }
    }
</script>

<Panel title="Synchronize Feed">
    <div class="gfat-actions">

        <div class="notice is-dismissible">
            <p>
                For the sync operation to be successful this feed, <em>"{feed.meta.feedName}"</em>,
                should be setup such that the <strong>Gravity Form Entry ID</strong> <em>is mapped
                to the</em> <strong>First Airtable Base Table Column</strong>.
            </p>
        </div>

        <br/>

        <StateButton
                on:start={start}
                on:stop={stop}
                startText={`Synchronize Feed : <strong>${feed.meta.feedName}</strong>`}
        />

    </div>
    <div class="gfat-progress">

        {#if $state.total}
            <ProgressBar max={$state.total} value={$state.processed}/>
        {/if}

    </div>
</Panel>
