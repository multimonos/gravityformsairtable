import { writable } from "svelte/store"


const defaults = {
    total: null,
    processed: 0,
    inProgress: false,
    isComplete: false,
    errorMessage: false,
}

const createStateStore = () => {

    const { subscribe, set, update } = writable( { ...defaults } )


    const reset = () =>
        update( s => {
            s.processed = 0
            s.total = null
            s.inProgress = false
            s.isComplete = false
            s.errorMessage = false
            return s
        } )

    return {
        subscribe,
        set,
        update,
        reset,
    }
}

export const state = createStateStore()
