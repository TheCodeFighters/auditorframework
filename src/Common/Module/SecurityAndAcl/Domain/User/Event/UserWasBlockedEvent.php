<?php
declare(strict_types=1);

namespace AuditorFramework\Common\Module\SecurityAndAcl\Domain\User\Event;

use AuditorFramework\Common\Types\Domain\Event\Event;
use AuditorFramework\Common\Module\SecurityAndAcl\Domain\User\BlockControl;
use AuditorFramework\Common\Module\SecurityAndAcl\Domain\User\UserId;

class UserWasBlockedEvent extends Event
{

    /** @var BlockControl */
    private $blockControl;

    /**
     * UserWasBlockedEvent constructor.
     * @param UserId $id
     * @param BlockControl $blockControl
     */
    public function __construct(UserId $id, BlockControl $blockControl)
    {
        $this->blockControl = $blockControl;
        parent::__construct($id);
    }

    protected function getIdClass(): string
    {
        return UserId::class;
    }

    protected function internalUnSerialize(): void
    {
        $payload = $this->payload();

        $this->blockControl = new BlockControl(
            $payload['is_blocked'],
            $payload['block_date']
        );
    }

    public function internalSerialize(): array
    {
        return [
            'is_blocked' => $this->blockControl->isBlocked(),
            'block_date' => $this->blockControl->blockDate()->format(DATE_ATOM)
        ];
    }

    /**
     * @return BlockControl
     */
    public function blockControl(): BlockControl
    {
        return $this->blockControl;
    }
}
