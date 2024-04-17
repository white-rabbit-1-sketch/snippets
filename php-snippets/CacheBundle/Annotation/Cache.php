<?php

namespace Shared\CacheBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Cache
 * @package Shared\CacheBundle\Annotation
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Cache extends Annotation
{
    /** @var int */
    public $ttl = 600;

    /** @var array */
    public $tags = [];
}