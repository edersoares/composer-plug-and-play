<?php

namespace Dex\Composer\PlugAndPlay\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a Composer instance cannot be created.
 */
class ComposerCreationException extends RuntimeException
{
    /**
     * Create a new ComposerCreationException instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}