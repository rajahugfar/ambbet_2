<?php

namespace JakubOnderka\PhpParallelLint\Process;

class PhpProcess extends Process
{
    /**
     * @param PhpExecutable $phpExecutable
     * @param array $parameters
     * @param string|null $stdIn
     */
    public function __construct(PhpExecutable $phpExecutable, array $parameters = array(), $stdIn = null)
    {
        $constructedParameters = $this->constructParameters($parameters, $phpExecutable->isIsHhvmType());
        $cmdLine = escapeshellcmd($phpExecutable->getPath()) . ' ' . $constructedParameters;
        parent::__construct($cmdLine, $stdIn);
    }

    /**
     * @param array $parameters
     * @param bool $isHhvm
     * @return string
     */
    private function constructParameters(array $parameters, $isHhvm)
    {
        return ($isHhvm ? '--php ' : '') . implode(' ', $parameters);
    }
}
