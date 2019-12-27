<?php

namespace AuditorFramework\Common\Types\Infrastructure\Exception;

class CsvKeyNotFoundException extends InvalidArgumentInfrastructureException
{

    protected function getName(): string
    {
        return 'csv key not found';
    }
}
