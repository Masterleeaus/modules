# Node Media Capture Pipeline

## Purpose
Defines how PWA nodes should capture, stage, queue, and reconcile photos/files/audio/video generated on device.

## Supported capture classes
- job proof photos
- inspection images
- issue/defect media
- voice notes
- document/photo uploads
- signature artifacts

## Pipeline
1. capture locally
2. attach local metadata
3. write file reference into local storage
4. create sync envelope with media placeholder
5. upload when network + policy allow
6. reconcile remote asset ID back into local record

## Required metadata
- local media id
- tenant/company id
- user id
- node id
- parent record reference
- capture timestamp
- media type + mime
- hash/checksum when available

## Local-first rules
- never block user workflow on immediate upload
- keep placeholder state visible in UI
- preserve capture order for proof chains
- store thumbnails/previews separately from original file when useful

## Failure handling
- upload retry queue
- dead-letter for corrupted/oversized assets
- conflict state if parent record changed before upload
- manual resend action for operator recovery

## Security
- avoid leaking raw local file paths in envelopes
- scrub metadata not required by business flow
- use signed or authenticated upload paths
