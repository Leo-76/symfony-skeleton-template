<?php

declare(strict_types=1);

namespace App\Message;

/**
 * Dispatched after a user registers to send a welcome email asynchronously.
 */
final class SendWelcomeEmail
{
    public function __construct(
        public readonly string $userId,
    ) {
    }
}
