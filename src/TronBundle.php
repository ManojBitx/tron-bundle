<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\DependencyInjection\TronExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TronBundle extends Bundle
{

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new TronExtension();
        }

        return $this->extension;
    }

}
