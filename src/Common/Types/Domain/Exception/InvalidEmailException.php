<?php
declare(strict_types=1);

namespace AuditorFramework\Common\Types\Domain\Exception;


class InvalidEmailException extends InvalidDomainFormatException
{

    protected function getName(): string
    {
        return 'email';
    }

}
