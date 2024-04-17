<?php

namespace Shared\CommandBundle\Command;

use Shared\UtilBundle\Entity\TraitParameterList;

/**
 * Class AbstractCommand
 * @package Shared\CommandBundle\Command
 */
abstract class AbstractCommand
{
    use TraitParameterList;

    /**
     * AbstractCommand constructor.
     * @param array $parameterList
     */
    public function __construct(array $parameterList = [])
    {
        $this->parameterList = $parameterList;
    }

    /**
     * @return string
     */
    abstract public function getName(): string;
}