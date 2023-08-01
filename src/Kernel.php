<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        if ($this->isLambda()) {
            return '/tmp/cache/' . $this->environment;
        }

        return parent::getCacheDir();
    }

    public function getLogDir(): string
    {
        if ($this->isLambda()) {
            return '/tmp/logs/' . $this->environment;
        }

        return parent::getLogDir();
    }

    public function getBuildDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    private function isLambda(): bool
    {
        return getenv('LAMBDA_TASK_ROOT') !== false;
    }
}
