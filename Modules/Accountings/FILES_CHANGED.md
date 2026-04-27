# Sidebar Fix

## Purpose
Fix the module sidebar to be cleaner-friendly and match the intended Books navigation.

## Changes
- Resources/views/sections/sidebar.blade.php
  - Renamed menu label to Books
  - Reduced sidebar items to a shallow, operator-friendly set
  - Removed technical submenus (COA, Journal Types, Journals) from the sidebar
- Resources/views/sections/setting-sidebar.blade.php
  - Default title uses Books label
- Resources/lang/en/app.php (+ eng/app.php if present)
  - Menu label now displays Books / Books Settings
