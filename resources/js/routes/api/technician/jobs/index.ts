import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import checklist from './checklist'
import photos from './photos'
import lineItems from './line-items'
/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
export const today = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: today.url(options),
    method: 'get',
})

today.definition = {
    methods: ["get","head"],
    url: '/api/technician/jobs/today',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
today.url = (options?: RouteQueryOptions) => {
    return today.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
today.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: today.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
today.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: today.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
export const show = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/technician/jobs/{job}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
show.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                }

    return show.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
show.get = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
show.head = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateStatus
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
export const updateStatus = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateStatus
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
updateStatus.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                }

    return updateStatus.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateStatus
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
updateStatus.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateNotes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
export const updateNotes = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateNotes.url(args, options),
    method: 'patch',
})

updateNotes.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/notes',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateNotes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
updateNotes.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                }

    return updateNotes.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateNotes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
updateNotes.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateNotes.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateCustomerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
export const updateCustomerNotes = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCustomerNotes.url(args, options),
    method: 'patch',
})

updateCustomerNotes.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/customer-notes',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateCustomerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
updateCustomerNotes.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                }

    return updateCustomerNotes.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateCustomerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
updateCustomerNotes.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCustomerNotes.url(args, options),
    method: 'patch',
})
const jobs = {
    today: Object.assign(today, today),
show: Object.assign(show, show),
updateStatus: Object.assign(updateStatus, updateStatus),
updateNotes: Object.assign(updateNotes, updateNotes),
updateCustomerNotes: Object.assign(updateCustomerNotes, updateCustomerNotes),
checklist: Object.assign(checklist, checklist),
photos: Object.assign(photos, photos),
lineItems: Object.assign(lineItems, lineItems),
}

export default jobs