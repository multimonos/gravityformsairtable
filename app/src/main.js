import App from './App.svelte'
import { config } from "./Store/ConfigStore"
import {createFeedListPageActionLinks} from "./lib/feed-list-page"


// evolve the action links on the Feed List pages.
createFeedListPageActionLinks()

// get config from wordpress
if ( window.gfAirtable_strings ) {
    config.fromWordpress( window.gfAirtable_strings )
}

// init
const app = new App( {
    target: document.querySelector( '#gf-airtable-app' ),
} )

export default app
