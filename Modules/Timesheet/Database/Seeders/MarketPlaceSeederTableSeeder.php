<?php

namespace Modules\Timesheet\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\LandingPage\Entities\MarketplacePageSetting;

class MarketPlaceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Timesheet';
        $data['product_main_description'] = '<p>Time recording is a crucial task in company operations which serves as a base for planning works according to the agenda. When you initiate a new project, it is very important to have a precise idea about the distribution of time for the tasks included in the project. During the course of the project, recording the time spent on each task will help you to examine the progress of that project.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = '<h2>Project <b>Timsheet</b></h2>';
        $data['dedicated_theme_description'] = '<p>Create a timesheet by assigning Projects, Tasks, and Users. Assign a starting and ending date as well as a time. This allows you to manage your project most efficiently.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Get Work Done On Time","dedicated_theme_section_description":"<p>Generate timesheets and keep an eye on how long your employees take to complete their tasks. Easily track time on all the tasks that you complete - and allow your employees to do the same.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"What is the use of Timesheet?","dedicated_theme_section_description":"<p>A timesheet is a data table which an employer can use to track the time a particular employee has worked during a certain period. Businesses use timesheets to record time spent on tasks, projects, or clients.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Timesheet"},{"screenshots":"","screenshots_heading":"Timesheet"},{"screenshots":"","screenshots_heading":"Timesheet"},{"screenshots":"","screenshots_heading":"Timesheet"},{"screenshots":"","screenshots_heading":"Timesheet"}]';
        $data['addon_heading'] = '<h2>Why choose dedicated modules<b> for Your Business?</b></h2>';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modulesfor Your Business?';
        $data['whychoose_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['pricing_plan_heading'] = 'Empower Your Workforce with DASH';
        $data['pricing_plan_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!</p>';
        $data['pricing_plan_demo_link'] = '#';
        $data['pricing_plan_demo_button_text'] = 'View Live Demo';
        $data['pricing_plan_text'] = '{"1":{"title":"Pay-as-you-go"},"2":{"title":"Unlimited installation"},"3":{"title":"Secure cloud storage"}}';
        $data['whychoose_sections_status'] = 'on';
        $data['dedicated_theme_section_status'] = 'on';

        foreach($data as $key => $value){
            if(!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', 'Timesheet')->exists()){
                MarketplacePageSetting::updateOrCreate(
                [
                    'name' => $key,
                    'module' => 'Timesheet'

                ],
                [
                    'value' => $value
                ]);
            }
        }
    }
}
