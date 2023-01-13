<script>
    import { state } from "../Store/StateStore"
    import { config } from "../Store/ConfigStore"
    import StateButton from "../Component/StateButton.svelte"
    import ProgressBar from "../Component/ProgressBar.svelte"
    import { ApiService } from "../Service/ApiService"
    import Panel from "../Component/Panel.svelte"

    // props
    export let feed

    // vars
    let api = ApiService( $config.siteUrl, $config.nonce )

    // fns
    const resetFeed = async ( id ) => {

        const p = new Promise( ( resolve, reject ) => {

            const loop = async () => {

                // exit : halt by user
                if ( ! $state.inProgress ) {
                    return resolve( true )
                }

                // process
                const res = await api.feedReset( id )

                if ( ! res.ok ) {
                    return reject( res )
                }

                const json = await res.json()

                // update state
                $state.processed =  $state.total - json.feed_sync_status.synced

                // exit : complete
                if ( json.feed_sync_status.synced === 0 ) {
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

    const start = async ( e ) => {
        try {
            if ( ! confirm( 'Are you sure?\n\nTHIS ACTION CANNOT BE UNDONE!' ) ) return
            if ( ! confirm( 'Are you really sure?' ) ) return

            // don't double do
            if ( $state.inProgress ) return

            // reset state
            state.reset()
            $state.total = feed.sync_status.synced
            $state.inProgress = true

            await resetFeed( feed.id )
            $state.inProgress = false

        } catch ( e ) {
            $state.errorMessage = `${ e.status } : ${ e.statusText }`
            $state.inProgress = false
            console.error( 'error', e )
        }
    }

    const stop = async e => {
        $state.inProgress = false
    }
</script>

<Panel title="Reset Feed"}>


    <div class="gfat-actions">
        <div class="notice notice-warning notice-alt">
            <p>
                Resetting the feed will completely erase any history with respect which
                Entries have been pushed to Airtable. The next time you synchronize the
                feed <strong>all entries will be pushed Airtable</strong>.
            </p>
        </div>
        <br/>
        <StateButton
                on:start={start}
                on:stop={stop}
                startText={`Reset Feed : <strong>${feed.meta.feedName}</strong>`}
        />
    </div>

    <div class="gfat-progress">
        {#if $state.total}
            <ProgressBar max={$state.total} value={$state.processed}/>
        {/if}
    </div>


</Panel>