<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\MessageTemplate;
use App\Models\OrganizationSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;
use Inertia\ResponseFactory;

class SetupController extends Controller
{
    /**
     * The ordered list of setup steps.
     * Required steps must be completed before setup is considered done.
     */
    public const STEPS = [
        'company'      => ['label' => 'Company Details',         'required' => true],
        'job_types'    => ['label' => 'Service Types',            'required' => true],
        'technicians'  => ['label' => 'Team Members',             'required' => true],
        'templates'    => ['label' => 'Notification Templates',   'required' => false],
        'branding'     => ['label' => 'Branding',                 'required' => false],
        'payment'      => ['label' => 'Payment Setup',            'required' => false],
    ];

    /**
     * Return true if the organization has completed all required setup steps.
     */
    public static function isComplete(int $orgId): bool
    {
        $settings = OrganizationSetting::where('organization_id', $orgId)->first();

        // Fast-path: setup_complete flag already set
        if ($settings && $settings->setup_complete) {
            return true;
        }

        if (! $settings || empty($settings->company_name)) {
            return false;
        }
        if (! JobType::where('organization_id', $orgId)->exists()) {
            return false;
        }
        if (! User::where('organization_id', $orgId)->role('technician')->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Return the array of completed step keys for the given org.
     */
    public static function completedSteps(int $orgId): array
    {
        $settings = OrganizationSetting::where('organization_id', $orgId)->first();

        if (! $settings) {
            return [];
        }

        // Derive from data if the persisted array is missing
        $steps = $settings->setup_completed_steps ?? [];

        if (! in_array('company', $steps) && ! empty($settings->company_name)) {
            $steps[] = 'company';
        }
        if (! in_array('job_types', $steps) && JobType::where('organization_id', $orgId)->exists()) {
            $steps[] = 'job_types';
        }
        if (! in_array('technicians', $steps) && User::where('organization_id', $orgId)->role('technician')->exists()) {
            $steps[] = 'technicians';
        }

        return array_values(array_unique($steps));
    }

    /**
     * Persist a completed step key to the organization settings.
     */
    private static function markStepComplete(int $orgId, string $step): void
    {
        $settings = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $existing = $settings->setup_completed_steps ?? [];

        if (! in_array($step, $existing)) {
            $existing[] = $step;
            $settings->update(['setup_completed_steps' => array_values($existing)]);
        }
    }

    // ── Routes ────────────────────────────────────────────────────────────────

    public function show(Request $request): Response|ResponseFactory|RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        if (self::isComplete($orgId)) {
            return redirect()->route('owner.dashboard');
        }

        $settings    = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $jobTypes    = JobType::where('organization_id', $orgId)->get(['id', 'name', 'color']);
        $technicians = User::where('organization_id', $orgId)->role('technician')->get(['id', 'name', 'email']);

        // Load notification templates (keyed by "event.channel")
        $dbTemplates = MessageTemplate::where('organization_id', $orgId)->get();
        $templates = [];
        foreach ($dbTemplates as $tpl) {
            $templates[$tpl->event . '.' . $tpl->channel] = [
                'subject' => $tpl->subject,
                'body'    => $tpl->body,
                'active'  => $tpl->is_active,
            ];
        }

        return inertia('Owner/Setup/Wizard', [
            'company' => [
                'name'    => $settings->company_name,
                'email'   => $settings->company_email,
                'phone'   => $settings->company_phone,
                'address' => $settings->company_address,
                'city'    => $settings->company_city,
                'state'   => $settings->company_state,
                'zip'     => $settings->company_zip,
            ],
            'job_types'          => $jobTypes,
            'technicians'        => $technicians,
            'templates'          => $templates,
            'branding' => [
                'brand_color'          => $settings->brand_color,
                'customer_facing_name' => $settings->customer_facing_name,
                'logo_path'            => $settings->logo_path,
            ],
            'setup_completed_steps' => self::completedSteps($orgId),
            'steps'                 => self::STEPS,
            'template_events'       => MessageTemplate::events(),
            'template_variables'    => MessageTemplate::variables(),
        ]);
    }

    // ── Step 1: Company details ───────────────────────────────────────────────

    public function saveCompany(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city'    => ['nullable', 'string', 'max:100'],
            'state'   => ['nullable', 'string', 'max:100'],
            'zip'     => ['nullable', 'string', 'max:20'],
        ]);

        $settings = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $settings->update([
            'company_name'    => $data['name'],
            'company_email'   => $data['email'] ?? null,
            'company_phone'   => $data['phone'] ?? null,
            'company_address' => $data['address'] ?? null,
            'company_city'    => $data['city'] ?? null,
            'company_state'   => $data['state'] ?? null,
            'company_zip'     => $data['zip'] ?? null,
        ]);

        self::markStepComplete($orgId, 'company');

        return back()->with('success', 'Company info saved.');
    }

    // ── Step 2: Job types ─────────────────────────────────────────────────────

    public function addJobType(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100', Rule::unique('job_types')->where('organization_id', $orgId)],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        JobType::create([
            'organization_id' => $orgId,
            'name'            => $data['name'],
            'color'           => $data['color'],
        ]);

        self::markStepComplete($orgId, 'job_types');

        return back()->with('success', 'Job type added.');
    }

    public function removeJobType(Request $request, JobType $jobType): RedirectResponse
    {
        abort_unless($jobType->organization_id === $request->user()->organization_id, 403);
        $jobType->delete();

        return back()->with('success', 'Job type removed.');
    }

    // ── Step 3: Technicians ───────────────────────────────────────────────────

    public function addTechnician(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'organization_id'   => $orgId,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('technician');

        self::markStepComplete($orgId, 'technicians');

        return back()->with('success', 'Technician added.');
    }

    // ── Step 4: Notification templates ───────────────────────────────────────

    public function saveNotificationTemplates(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $validEvents   = array_keys(MessageTemplate::events());
        $validChannels = ['email', 'sms'];

        $data = $request->validate([
            'templates'                        => ['required', 'array'],
            'templates.*.event'                => ['required', 'string', Rule::in($validEvents)],
            'templates.*.channel'              => ['required', 'string', Rule::in($validChannels)],
            'templates.*.subject'              => ['nullable', 'string', 'max:255'],
            'templates.*.body'                 => ['required', 'string', 'max:2000'],
            'templates.*.is_active'            => ['boolean'],
        ]);

        foreach ($data['templates'] as $tpl) {
            MessageTemplate::updateOrCreate(
                [
                    'organization_id' => $orgId,
                    'event'           => $tpl['event'],
                    'channel'         => $tpl['channel'],
                ],
                [
                    'subject'   => $tpl['subject'] ?? null,
                    'body'      => $tpl['body'],
                    'is_active' => $tpl['is_active'] ?? true,
                ]
            );
        }

        self::markStepComplete($orgId, 'templates');

        return back()->with('success', 'Notification templates saved.');
    }

    // ── Step 4 (skip) ────────────────────────────────────────────────────────

    public function skipStep(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'step' => ['required', 'string', Rule::in(array_keys(self::STEPS))],
        ]);

        $stepConfig = self::STEPS[$data['step']];

        if ($stepConfig['required']) {
            return back()->withErrors(['step' => 'This step is required and cannot be skipped.']);
        }

        self::markStepComplete($orgId, $data['step']);

        return back()->with('success', 'Step skipped.');
    }

    // ── Step 5: Branding ─────────────────────────────────────────────────────

    public function saveBranding(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'brand_color'          => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'customer_facing_name' => ['nullable', 'string', 'max:255'],
        ]);

        $settings = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $settings->update([
            'brand_color'          => $data['brand_color'] ?? null,
            'customer_facing_name' => $data['customer_facing_name'] ?? null,
        ]);

        self::markStepComplete($orgId, 'branding');

        return back()->with('success', 'Branding saved.');
    }

    // ── Step 6: Payment setup (Stripe Connect) ────────────────────────────────

    public function markPaymentComplete(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        self::markStepComplete($orgId, 'payment');

        return back()->with('success', 'Payment step marked complete.');
    }

    // ── Step 7: Complete ──────────────────────────────────────────────────────

    public function complete(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        if (! self::isComplete($orgId)) {
            return back()->withErrors(['setup' => 'Please complete all required setup steps before continuing.']);
        }

        // Persist the completion flag
        $settings = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $settings->update(['setup_complete' => true]);

        return redirect()->route('owner.dashboard')->with('setup_complete', true);
    }
}
