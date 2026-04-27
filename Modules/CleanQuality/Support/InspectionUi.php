<?php

namespace Modules\CleanQuality\Support;

class InspectionUi
{
    public static function moduleLabel(): string
    {
        return __('quality_control::modules.quality_control.module')
            ?: __('quality_control::sidebar.module_name');
    }
}
