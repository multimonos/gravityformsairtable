export const ApiService = ( baseuri, nonce ) => {

    const headers = {
        "Content-type": "application/json",
        "X-WP-Nonce": nonce,
    }

    const feedSync = async ( id ) => {
        const method = "POST"
        const url = `${ baseuri }/wp-json/gfairtable/v1/feeds/${ id }/sync`

        return fetch( url, {
            method,
            headers,
        } )
    }

    const feedReset = async ( id ) => {
        const method = "POST"
        const url = `${ baseuri }/wp-json/gfairtable/v1/feeds/${ id }/reset`

        return fetch( url, {
            method,
            headers,
        } )
    }

    const feedEntryMeta = async ( feed_id, entry_id ) => {
        const url = `${ baseuri }/wp-json/gfairtable/v1/feeds/${ feed_id }/entries/${ entry_id }/meta`
        return fetch( url, { headers } )
    }

    const feed = async id => {
        const url = `${ baseuri }/wp-json/gfairtable/v1/feeds/${ id }`
        return fetch( url, { headers } )
    }


    return {
        feed,
        feedSync,
        feedReset,
        feedEntryMeta,
    }
}
