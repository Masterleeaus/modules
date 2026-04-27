<?php
namespace Modules\Accountings\Helpers;

class ReconciliationScore {
  public static function score(float $amountDiff, int $daysDiff): float {
    $amountScore = max(0, 1 - $amountDiff);
    $dateScore = max(0, 1 - ($daysDiff / 7));
    return round(($amountScore * 0.7 + $dateScore * 0.3), 2);
  }
}
