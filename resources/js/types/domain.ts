// ─── Minimal User reference used inside relations ────────────────────────────
// The full User interface lives in @/types/index.d.ts; this leaner version is
// used to avoid circular imports when domain types reference each other.

export interface UserRef {
    id: number;
    name: string;
    email: string;
    avatar?: string;
}

// ─── Status / role unions ────────────────────────────────────────────────────

export type JobStatus =
    | 'scheduled'
    | 'en_route'
    | 'in_progress'
    | 'completed'
    | 'cancelled'
    | 'on_hold';

export type InvoiceStatus = 'draft' | 'sent' | 'paid' | 'partial' | 'overdue' | 'void';

export type EstimateStatus = 'draft' | 'sent' | 'accepted' | 'declined' | 'expired';

export type PaymentMethod = 'cash' | 'check' | 'card' | 'bank_transfer' | 'stripe';

export type PaymentStatus = 'pending' | 'completed' | 'failed' | 'refunded';

export type JobCrewRole = 'lead' | 'support';

export type RecurringFrequency = 'weekly' | 'biweekly' | 'monthly' | 'custom';

export type SubscriptionStatus = 'trialing' | 'active' | 'past_due' | 'canceled' | 'paused';

export type EstimateTier = 'good' | 'better' | 'best';

// ─── Core domain entities ─────────────────────────────────────────────────────

export interface Organization {
    id: number;
    name: string;
    slug: string;
    timezone: string | null;
    plan: string;
    trial_ends_at: string | null;
    stripe_customer_id: string | null;
    created_at: string;
    updated_at: string;
}

export interface Customer {
    id: number;
    organization_id: number;
    first_name: string;
    last_name: string;
    /** Accessor: `{first_name} {last_name}` */
    full_name?: string;
    email: string | null;
    phone: string | null;
    mobile: string | null;
    notes: string | null;
    reminder_preference: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    // Relations (optional – only present when eager-loaded)
    properties?: Property[];
    jobs?: Job[];
    invoices?: Invoice[];
}

export interface Property {
    id: number;
    organization_id: number;
    customer_id: number;
    name: string | null;
    address_line1: string;
    address_line2: string | null;
    city: string;
    state: string;
    postal_code: string;
    country: string | null;
    latitude: string | null;
    longitude: string | null;
    notes: string | null;
    /** Accessor: comma-joined address parts */
    full_address?: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    // Relations (optional)
    customer?: Customer;
    jobs?: Job[];
}

export interface JobType {
    id: number;
    organization_id: number;
    name: string;
    color: string | null;
    description: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    checklist_items?: JobTypeChecklistItem[];
}

export interface JobTypeChecklistItem {
    id: number;
    job_type_id: number;
    label: string;
    section: string | null;
    sort_order: number;
    is_required: boolean;
    created_at: string;
    updated_at: string;
}

export interface Job {
    id: number;
    organization_id: number;
    customer_id: number;
    property_id: number | null;
    job_type_id: number | null;
    estimate_id: number | null;
    recurring_template_id: number | null;
    assigned_to: number | null;
    title: string;
    description: string | null;
    status: JobStatus;
    scheduled_at: string | null;
    started_at: string | null;
    arrived_at: string | null;
    completed_at: string | null;
    cancelled_at: string | null;
    reminder_sent_24h_at: string | null;
    reminder_sent_2h_at: string | null;
    technician_notes: string | null;
    customer_notes: string | null;
    office_notes: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    // Relations (optional – only present when eager-loaded; nullable when FK is nullable)
    customer?: Customer | null;
    property?: Property | null;
    job_type?: JobType | null;
    assigned_technician?: UserRef | null;
    line_items?: JobLineItem[];
    checklist_items?: JobChecklistItem[];
    crew?: JobCrew[];
    attachments?: Attachment[];
    messages?: JobMessage[];
    invoice?: Invoice | null;
    review?: JobReview | null;
    supply_usages?: JobSupplyUsage[];
    recurring_template?: RecurringJobTemplate | null;
}

export interface JobLineItem {
    id: number;
    job_id: number;
    item_id: number | null;
    name: string;
    description: string | null;
    unit_price: string;
    quantity: string;
    /** Computed column: unit_price × quantity */
    total: string;
    sort_order: number;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    item?: Item;
}

export interface JobChecklistItem {
    id: number;
    job_id: number;
    job_type_checklist_item_id: number | null;
    label: string;
    section: string | null;
    sort_order: number;
    is_required: boolean;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface JobCrew {
    id: number;
    job_id: number;
    user_id: number;
    role: JobCrewRole;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    user?: UserRef;
}

export interface JobMessage {
    id: number;
    job_id: number;
    customer_id: number | null;
    channel: string;
    event: string | null;
    recipient: string | null;
    body: string;
    status: string;
    error: string | null;
    created_at: string;
    updated_at: string;
}

export interface JobReview {
    id: number;
    job_id: number;
    customer_id: number;
    technician_id: number | null;
    rating: number;
    comment: string | null;
    tip_amount: string | null;
    created_at: string;
    updated_at: string;
}

export interface JobSupplyUsage {
    id: number;
    job_id: number;
    item_id: number;
    quantity_used: string;
    notes: string | null;
    recorded_by: number | null;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    item?: Item;
}

export interface Invoice {
    id: number;
    organization_id: number;
    customer_id: number;
    job_id: number | null;
    invoice_number: string | null;
    status: InvoiceStatus;
    subtotal: string;
    tax_rate: string;
    tax_amount: string;
    discount_amount: string;
    total: string;
    amount_paid: string;
    balance_due: string;
    issued_at: string | null;
    due_at: string | null;
    sent_at: string | null;
    paid_at: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    // Relations (optional – nullable when FK is nullable)
    customer?: Customer | null;
    job?: Job | null;
    line_items?: InvoiceLineItem[];
    payments?: Payment[];
    attachments?: Attachment[];
}

export interface InvoiceLineItem {
    id: number;
    invoice_id: number;
    item_id: number | null;
    name: string;
    description: string | null;
    unit_price: string;
    quantity: string;
    /** Computed column: unit_price × quantity */
    total: string;
    is_taxable: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    item?: Item;
}

export interface Payment {
    id: number;
    organization_id: number;
    invoice_id: number;
    /**
     * Holds the FK integer when the model is serialised without loading the relation.
     * When `recordedBy()` is eager-loaded, Laravel replaces this key with the
     * full UserRef object. Use `PaymentWithRecordedBy` when the relation is loaded.
     */
    recorded_by: number | UserRef | null;
    amount: string;
    method: PaymentMethod;
    reference: string | null;
    stripe_payment_intent_id: string | null;
    status: string;
    paid_at: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    invoice?: Invoice;
}

export interface Estimate {
    id: number;
    organization_id: number;
    customer_id: number;
    job_id: number | null;
    estimate_number: string | null;
    title: string;
    intro: string | null;
    footer: string | null;
    status: EstimateStatus;
    token: string;
    expires_at: string | null;
    sent_at: string | null;
    accepted_at: string | null;
    accepted_package: EstimateTier | null;
    declined_at: string | null;
    tax_rate: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    // Relations (optional – nullable when FK is nullable)
    customer?: Customer | null;
    job?: Job | null;
    packages?: EstimatePackage[];
    converted_job?: Job | null;
}

export interface EstimatePackage {
    id: number;
    estimate_id: number;
    tier: EstimateTier;
    label: string;
    description: string | null;
    subtotal: string;
    tax_amount: string;
    total: string;
    is_recommended: boolean;
    frequency: string | null;
    frequency_discount: string | null;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    line_items?: EstimateLineItem[];
}

export interface EstimateLineItem {
    id: number;
    estimate_package_id: number;
    item_id: number | null;
    name: string;
    description: string | null;
    unit_price: string;
    quantity: string;
    /** Computed column: unit_price × quantity */
    total: string;
    is_taxable: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
    // Relations (optional)
    item?: Item;
}

export interface Item {
    id: number;
    organization_id: number;
    name: string;
    sku: string | null;
    description: string | null;
    unit_price: string;
    unit: string | null;
    is_taxable: boolean;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface MessageTemplate {
    id: number;
    organization_id: number;
    event: string;
    channel: string;
    subject: string | null;
    body: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface RecurringJobTemplate {
    id: number;
    organization_id: number;
    customer_id: number;
    property_id: number | null;
    job_type_id: number | null;
    assigned_to: number | null;
    title: string;
    description: string | null;
    frequency: RecurringFrequency;
    recurrence_rule: string | null;
    start_date: string;
    end_date: string | null;
    price: string | null;
    is_active: boolean;
    last_generated_on: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    // Relations (optional)
    customer?: Customer;
    property?: Property;
    job_type?: JobType;
}

export interface Attachment {
    id: number;
    organization_id: number;
    uploaded_by: number | null;
    attachable_type: string;
    attachable_id: number;
    filename: string;
    disk: string;
    path: string;
    mime_type: string;
    size: number;
    tag: string | null;
    attachment_type: string | null;
    /** Accessor: public URL for the file */
    url: string;
    created_at: string;
    updated_at: string;
}

export interface OrganizationSetting {
    id: number;
    organization_id: number;
    company_name: string | null;
    company_email: string | null;
    company_phone: string | null;
    company_address: string | null;
    company_city: string | null;
    company_state: string | null;
    company_zip: string | null;
    company_website: string | null;
    logo_path: string | null;
    default_tax_rate: string | null;
    brand_color: string | null;
    customer_facing_name: string | null;
    setup_completed_steps: string[] | null;
    setup_complete: boolean;
    // Stripe – secret key is hidden server-side but shown masked on the integrations page
    stripe_secret_key: string | null;
    stripe_publishable_key: string | null;
    stripe_webhook_secret: string | null;
    // Twilio
    twilio_account_sid: string | null;
    twilio_auth_token: string | null;
    twilio_from_number: string | null;
    // SendGrid
    sendgrid_api_key: string | null;
    sendgrid_from_email: string | null;
    // Google Maps
    google_maps_api_key: string | null;
    created_at: string;
    updated_at: string;
}

export interface Subscription {
    id: number;
    organization_id: number;
    plan: string;
    status: SubscriptionStatus;
    billing_interval: string | null;
    stripe_subscription_id: string | null;
    stripe_price_id: string | null;
    trial_ends_at: string | null;
    current_period_start: string | null;
    current_period_end: string | null;
    canceled_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface DriverLocation {
    id: number;
    user_id: number;
    latitude: string;
    longitude: string;
    heading: string | null;
    speed: string | null;
    recorded_at: string;
    created_at: string;
    updated_at: string;
}

export interface ClientPortalToken {
    id: number;
    customer_id: number;
    token: string;
    expires_at: string;
    used_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface CmsPage {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    meta_title: string | null;
    meta_description: string | null;
    status: string;
    website_content: Record<string, unknown> | null;
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface FoundingMemberCoupon {
    id: number;
    code: string;
    description: string | null;
    discount_percent: number;
    max_uses: number;
    uses: number;
    active: boolean;
    expires_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface PlatformSetting {
    id: number;
    app_name: string | null;
    site_name: string | null;
    logo: string | null;
    logo_path: string | null;
    favicon: string | null;
    favicon_path: string | null;
    primary_color: string | null;
    secondary_color: string | null;
    accent_color: string | null;
    support_email: string | null;
    billing_email: string | null;
    contact_phone: string | null;
    footer_text: string | null;
    meta_title: string | null;
    meta_description: string | null;
    landing_headline: string | null;
    landing_subheadline: string | null;
    cta_label: string | null;
    cta_url: string | null;
    enable_registration: boolean;
    maintenance_message: string | null;
    custom_css: string | null;
    created_at: string;
    updated_at: string;
}
