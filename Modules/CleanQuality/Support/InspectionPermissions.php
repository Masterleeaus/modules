<?php

namespace Modules\CleanQuality\Support;

use Modules\CleanQuality\Support\Contracts\PermissionProvider;

final class InspectionPermissions implements PermissionProvider
{
    public const MAP = [
        self::VIEW => self::LEGACY_VIEW,
        self::CREATE => self::LEGACY_CREATE,
        self::UPDATE => self::LEGACY_UPDATE,
        self::DELETE => self::LEGACY_DELETE,
    ];

    public const VIEW   = 'view_quality_control';
    public const CREATE = 'add_quality_control';
    public const UPDATE = 'edit_quality_control';
    public const DELETE = 'delete_quality_control';

    public const LEGACY_VIEW   = 'inspection.view';
    public const LEGACY_CREATE = 'inspection.create';
    public const LEGACY_UPDATE = 'inspection.update';
    public const LEGACY_DELETE = 'inspection.delete';

    public static function legacyFor(string $permission): ?string
    {
        return self::MAP[$permission] ?? null;
    }
}
