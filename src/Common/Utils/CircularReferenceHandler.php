<?php

declare(strict_types=1);

namespace App\Common\Utils;

/**
 * Хэндлер для решения проблемы с циклицескими ссылками.
 *
 * @package App\Utils
 */
class CircularReferenceHandler
{
    public function __invoke($object)
    {
        if (method_exists($object, 'getName')) {
            return $object->getName();
        }
        if (method_exists($object, 'getId')) {
            return $object->getId();
        }
        return null;
    }
}
