<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *   temporary command `php bin/console debug:asset-map` to see all paths.
 *
 * - "entrypoint" (optional) set to true for any module that will
 *   be used as an entrypoint (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
];
