<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\ModuleSetting;
use App\Scopes\CompanyScope;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

class RecruitSetting extends BaseModel
{

    use HasFactory, HasCompany;

    const MODULE_NAME = 'recruit';

    protected $fillable = [];

    protected $casts = [
        'mail_setting' => 'json',
        'form_settings' => 'json'
    ];

    protected $appends = [
        'logo_url',
        'bg_image_url',
    ];

    public function getBgImageUrlAttribute()
    {
        return ($this->background_image) ? asset_url_local_s3('background/' . $this->background_image) : asset('img/image-bg.jpg');
    }

    public function getLogoUrlAttribute()
    {
        return ($this->logo) ? asset_url_local_s3('company-logo/' . $this->logo) : $this->company->logo_url;
    }

    public static function addModuleSetting($company)
    {
        self::categoryStatus($company);
        self::recruitSettingInsert($company);
        self::permissions($company);
        self::stages($company);
        self::jobTypes($company);
        self::workExperience($company);
        self::notificationSetting($company);
        self::applicationSource($company);
    }

    private static function permissions($company)
    {
        $roles = ['employee', 'admin'];
        ModuleSetting::createRoleSettingEntry(self::MODULE_NAME, $roles, $company);
    }

    private static function stages($company)
    {
        $stages = [
            ['name' => 'HR round', 'company_id' => $company->id],
            ['name' => 'Technical round', 'company_id' => $company->id],
            ['name' => 'Manager round', 'company_id' => $company->id],
        ];

        foreach ($stages as $stage) {
            $setting = RecruitInterviewStage::withoutGlobalScope(CompanyScope::class)->firstOrNew($stage);
            $setting->saveQuietly();
        }
    }

    private static function categoryStatus($company)
    {
        $data = [
            [
                'name' => 'applied',
                'status' => [
                    'status' => 'applied',
                    'slug' => 'applied',
                    'position' => '1',
                    'color' => '#2b2b2b',
                    'action' => 'yes'
                ]
            ],
            [
                'name' => 'shortlist',
                'status' => [
                    'status' => 'phone screen',
                    'slug' => 'phone_screen',
                    'position' => '2',
                    'color' => '#f1e52e',
                    'action' => 'yes'
                ]
            ],
            [
                'name' => 'interview',
                'status' => [
                    'status' => 'interview',
                    'slug' => 'interview',
                    'position' => '3',
                    'color' => '#3d8ee8',
                    'action' => 'yes'
                ]
            ],
            [
                'name' => 'hired',
                'status' => [
                    'status' => 'hired',
                    'slug' => 'hired',
                    'position' => '4',
                    'color' => '#32ac16',
                    'action' => 'yes'
                ]
            ],
            [
                'name' => 'rejected',
                'status' => [
                    'status' => 'rejected',
                    'slug' => 'rejected',
                    'position' => '5',
                    'color' => '#ee1127',
                    'action' => 'yes'
                ]
            ],
            ['name' => 'others'],
        ];

        foreach ($data as $item) {

            $newData = RecruitApplicationStatusCategory::withoutGlobalScope(CompanyScope::class)->firstOrNew([
                'name' => $item['name'],
                'company_id' => $company->id,
            ]);

            $newData->saveQuietly();

            if (isset($item['status'])) {
                $item['status']['company_id'] = $company->id;
                $item['status']['recruit_application_status_category_id'] = $newData->id;
                $setting = RecruitApplicationStatus::withoutGlobalScope(CompanyScope::class)->firstOrNew($item['status']);
                $setting->saveQuietly();
            }
        }

    }

    public static function recruitSettingInsert($company)
    {
        $status = RecruitApplicationStatus::withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)->get();
        $arr = [];

        foreach ($status as $val) {
            $arr[$val->id] = [
                'id' => $val->id,
                'name' => $val->status,
                'status' => true
            ];
        }

        $formArr = [];

        $formArr[1] = [
            'id' => 1,
            'name' => 'email',
            'slug' => 'Email',
            'status' => false
        ];

        $formArr[2] = [
            'id' => 2,
            'name' => 'phone',
            'slug' => 'Phone',
            'status' => false
        ];
        $formArr[3] = [
            'id' => 3,
            'name' => 'gender',
            'slug' => 'Gender',
            'status' => false
        ];
        $formArr[4] = [
            'id' => 4,
            'name' => 'total_experience',
            'slug' => 'Total Experience',
            'status' => false
        ];
        $formArr[5] = [
            'id' => 5,
            'name' => 'current_location',
            'slug' => 'Current location',
            'status' => false
        ];
        $formArr[6] = [
            'id' => 6,
            'name' => 'current_ctc',
            'slug' => 'Current Ctc',
            'status' => false
        ];
        $formArr[7] = [
            'id' => 7,
            'name' => 'expected_ctc',
            'slug' => 'Expected Ctc',
            'status' => false
        ];
        $formArr[8] = [
            'id' => 8,
            'name' => 'notice_period',
            'slug' => 'Notice Period',
            'status' => false
        ];
        $formArr[9] = [
            'id' => 9,
            'name' => 'application_source',
            'slug' => 'Source',
            'status' => false
        ];
        $formArr[10] = [
            'id' => 10,
            'name' => 'status',
            'slug' => 'Status',
            'status' => false
        ];
        $formArr[11] = [
            'id' => 11,
            'name' => 'cover_letter',
            'slug' => 'Cover letter',
            'status' => false
        ];

        $setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)->first();

        if (Schema::hasColumn('recruit_settings', 'form_settings') && !is_null($setting)) {
            $setting->form_settings = $formArr;
            $setting->saveQuietly();
        }

        $data = '<header>
      <h5 >Dummy Company</h5>
      <p>A leader in innovative solutions</p>
    </header>
    <main class="mt-2">
      <h5 class="mt-2">About Us</h5>
      <p>Dummy Company was founded in 2020 with a mission to provide innovative solutions for a better tomorrow. We have a team of experts in various fields who work together to bring cutting-edge products and services to market. Our focus on quality and customer satisfaction has made us a trusted name in the industry.</p>
      <h5 class="mt-2">Our Products and Services</h5>
      <ul>
        <li>Product 1: A revolutionary new technology that improves efficiency and productivity</li>
        <li>Product 2: A user-friendly platform that simplifies complex processes</li>
        <li>Service 1: Customized consulting services to help businesses stay ahead of the curve</li>
        <li>Service 2: Training and support to ensure the successful adoption of our products</li>
      </ul>
      <h5 class="mt-2">Our Team</h5>
      <p>Dummy Company is powered by a talented and dedicated team of professionals. Our team members bring a diverse set of skills and experiences to the table, allowing us to tackle complex challenges and deliver solutions that truly make a difference. We are committed to fostering a positive and collaborative work environment where everyone has the opportunity to grow and succeed.</p>
    </main>
    <footer>';

        if (!$setting) {
            $newSetting = new RecruitSetting();
            $newSetting->company_id = $company->id;
            $newSetting->company_name = $company->company_name;
            $newSetting->company_website = $company->website;
            $newSetting->mail_setting = $arr;
            $newSetting->form_settings = $formArr;
            $newSetting->legal_term = "If any provision of these Terms and Conditions is held to be invalid or unenforceable, the provision shall be removed (or interpreted, if possible, in a manner as to be enforceable), and the remaining provisions shall be enforced. Headings are for reference purposes only and in no way define, limit, construe or describe the scope or extent of such section. Our failure to act with respect to a breach by you or others does not waive our right to act with respect to subsequent or similar breaches. These Terms and Conditions set forth the entire understanding and agreement between us with respect to the subject matter contained herein and supersede any other agreement, proposals and communications, written or oral, between our representatives and you with respect to the subject matter hereof, including any terms and conditions on any of customer's documents or purchase orders.<br>No Joint Venture, No Derogation of Rights. You agree that no joint venture, partnership, employment, or agency relationship exists between you and us as a result of these Terms and Conditions or your use of the Site. Our performance of these Terms and Conditions is subject to existing laws and legal process, and nothing contained herein is in derogation of our right to comply with governmental, court and law enforcement requests or requirements relating to your use of the Site or information provided to or gathered by us with respect to such use.";
            $newSetting->about = $data;
            $newSetting->saveQuietly();
        }
    }

    private static function jobTypes($company)
    {
        $types = ['Full time', 'Part time', 'On Contract', 'Internship', 'Trainee'];

        foreach ($types as $type) {
            $data = RecruitJobType::withoutGlobalScope(CompanyScope::class)->firstOrNew([
                'job_type' => $type,
                'company_id' => $company->id,
            ]);
            $data->saveQuietly();
        }
    }

    private static function workExperience($company)
    {
        $workExperiences = ['fresher', '0-1 years', '1-3 years', '3-5 years', '5+ years'];

        foreach ($workExperiences as $workExperience) {
            $data = RecruitWorkExperience::withoutGlobalScope(CompanyScope::class)->firstOrNew([
                'work_experience' => $workExperience,
                'company_id' => $company->id
            ]);
            $data->saveQuietly();
        }
    }

    private static function notificationSetting($company)
    {
        $data = [
            ['setting_name' => 'New Job/Added by Admin', 'send_email' => 'yes', 'slug' => 'new-jobadded-by-admin'],
            ['setting_name' => 'New Job Application/Added by Admin', 'send_email' => 'yes', 'slug' => 'new-job-applicationadded-by-admin'],
            ['setting_name' => 'New Interview Schedule/Added by Admin', 'send_email' => 'yes', 'slug' => 'new-interview-scheduleadded-by-admin'],
            ['setting_name' => 'New Offer Letter/Added by Admin', 'send_email' => 'yes', 'slug' => 'new-offer-letteradded-by-admin'],
            ['setting_name' => 'Notification to Recruiter', 'send_email' => 'yes', 'slug' => 'notification-to-recruiter'],
        ];

        foreach ($data as $item) {
            $item['company_id'] = $company->id;
            $setting = RecruitEmailNotificationSetting::withoutGlobalScope(CompanyScope::class)->firstOrNew($item);
            $setting->saveQuietly();
        }
    }


    private static function applicationSource($company)
    {
        $sourceList = [
            ['application_source' => 'LinkedIn', 'company_id' => $company->id, 'is_predefined' => true],
            ['application_source' => 'Facebook', 'company_id' => $company->id, 'is_predefined' => true],
            ['application_source' => 'Instagram', 'company_id' => $company->id, 'is_predefined' => true],
            ['application_source' => 'Twitter', 'company_id' => $company->id, 'is_predefined' => true],
            ['application_source' => 'Other', 'company_id' => $company->id, 'is_predefined' => true],
        ];

        ApplicationSource::insert($sourceList);
    }

}
