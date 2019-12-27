<?php
declare(strict_types=1);

namespace AuditorFramework\Common\Utils\Assertion;

use AuditorFramework\Common\Types\Domain\Exception\DomainException;

class DomainApplicationAssertionFailedException extends DomainException
{
    /**
     * @var string
     */
    private $errorMessage;

    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;

        parent::__construct();
    }

    protected function errorMessage(): string
    {
        return $this->errorMessage;
    }
}
