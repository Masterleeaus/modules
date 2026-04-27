<?php

namespace Modules\SupplyChain\Support\Enums;

enum StockMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjust = 'adjust';
}
