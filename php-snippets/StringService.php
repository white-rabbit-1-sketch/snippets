<?php

/**
Утилита
*/

namespace Shared\UtilBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class StringService
 * @package Shared\UtilBundle\Service
 *
 * @DI\Service("shared.util.service.string", public=true)
 */
class StringService
{
    protected const DEFAULT_RANDOM_STRING_LENGTH = 20;

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomString(int $length = self::DEFAULT_RANDOM_STRING_LENGTH): string
    {
        $string = '';

        while (True) {
            $string .= uniqid();
            if (mb_strlen($string) >= $length) {
                break;
            }
        }

        return str_shuffle(mb_substr($string, 0, $length));
    }

    /**
     * @param string $string
     * @return string
     */
    public function underscore(string $string): string
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string));
    }
}
