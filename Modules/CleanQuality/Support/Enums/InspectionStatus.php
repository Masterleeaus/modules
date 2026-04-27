<?php

namespace Modules\CleanQuality\Support\Enums;

final class InspectionStatus
{
    public const PENDING = 'pending';
    public const IN_PROGRESS = 'in_progress';
    public const PASSED = 'passed';
    public const FAILED = 'failed';
    public const RECLEAN_BOOKED = 'reclean_booked';
}
