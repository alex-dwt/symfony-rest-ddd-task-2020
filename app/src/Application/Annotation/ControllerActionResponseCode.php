<?php

declare(strict_types=1);

namespace App\Application\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ControllerActionResponseCode
{
    /**
     * @Required
     */
    public int $value;
}
