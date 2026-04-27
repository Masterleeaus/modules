<?php

namespace Modules\CleanQuality\Support\Dto;

final class RecurringScheduleDto
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $title = null,
    ) {}
}
