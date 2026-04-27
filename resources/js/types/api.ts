// ─── Laravel paginator shape ─────────────────────────────────────────────────

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

/**
 * The standard Laravel paginator response shape as returned by
 * `Model::paginate()` and serialised through Inertia / JSON.
 */
export interface PaginatedCollection<T> {
    data: T[];
    links: PaginationLink[];
    /** First item number on the current page (null when collection is empty). */
    from: number | null;
    /** Last item number on the current page (null when collection is empty). */
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
    path: string;
}

// ─── JSON API response envelopes ─────────────────────────────────────────────

export interface ApiMeta {
    timestamp: string;
    version?: string;
}

export interface ApiResponse<T> {
    success: true;
    data: T;
    meta: ApiMeta;
}

export interface ApiPaginationMeta extends ApiMeta {
    pagination: {
        current_page: number;
        per_page: number;
        total: number;
        last_page: number;
    };
}

export interface ApiCollectionResponse<T> {
    success: true;
    data: T[];
    meta: ApiPaginationMeta;
}

export interface ApiErrorResponse {
    success: false;
    error: {
        code: string;
        message: string;
        details?: Record<string, string[]>;
    };
    meta: ApiMeta;
}

/** Discriminated union covering all three possible API response shapes. */
export type ApiResult<T> = ApiResponse<T> | ApiCollectionResponse<T> | ApiErrorResponse;
