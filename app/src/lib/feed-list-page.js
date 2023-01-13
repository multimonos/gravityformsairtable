export const createFeedListPageActionLinks = () => {
    const links = document.querySelectorAll( '.gf-airtable-action' )

    if ( links === null ) return

    const dispatchFeedListAction = e => {
            e.preventDefault()

            const detail = {
                action: e.target.dataset.airtableAction,
                feed_id: e.target.dataset.airtableFeed,
            }

            event = new CustomEvent( 'airtable', { detail } )
            window.dispatchEvent( event )
    }

    // wire up the feed list actions
    links.forEach( node => {
        node.addEventListener( 'click', dispatchFeedListAction )
    } )
}