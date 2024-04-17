<?php

namespace Shared\CacheBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class InvalidateCache
 * @package Shared\CacheBundle\Annotation
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class InvalidateCache extends Annotation
{
    /** @var array */
    public $tags = [];
}