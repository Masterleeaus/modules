VIEWFIX NOTE

This package removes references to Blade components that are not present in your install:
- components.filters
- components.widget

If you still see "View [components.filters] not found", it means the old blade is still on disk or cached.

Verify on server:
  grep -R "components.filters" -n Modules/Asset/Resources/views

Clear compiled views:
  php artisan view:clear
  rm -f storage/framework/views/*.php
  php artisan optimize:clear
