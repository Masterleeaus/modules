<?php

namespace Modules\CleanQuality\Support\Dto;

final class InspectionSummaryDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
    ) {}
}
