import { writable } from "svelte/store"

const defaults = {
    siteUrl: 'https://cactus.test',
    pageSize: 10,
    rateLimit: 250, // .25 seconds as airtable api rate limit is 5 per second
    nonce: null,
}


const createConfigStore = () => {

    const { subscribe, set, update } = writable( { ...defaults } )

    return {
        subscribe,
        set,
        update,
        fromWordpress: values => update( config => {
            config = { ...config, ...values }
            return config
        } )
    }
}

export const config = createConfigStore()
