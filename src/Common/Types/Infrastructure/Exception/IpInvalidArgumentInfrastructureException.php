<?php

namespace AuditorFramework\Common\Types\Infrastructure\Exception;

class IpInvalidArgumentInfrastructureException extends InvalidArgumentInfrastructureException
{

    protected function getName(): string
    {
        return 'ip';
    }
}
