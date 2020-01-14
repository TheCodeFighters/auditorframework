<?php
declare(strict_types=1);

namespace TheCodeFighters\Bundle\AuditorFramework\Common\Module\SecurityAndAcl\Domain\User\Event;

use TheCodeFighters\Bundle\AuditorFramework\Common\Types\Domain\Event\Event;
use TheCodeFighters\Bundle\AuditorFramework\Common\Module\SecurityAndAcl\Domain\User\BlockControl;
use TheCodeFighters\Bundle\AuditorFramework\Common\Module\SecurityAndAcl\Domain\User\UserId;

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

    protected function internalUnSerializePayload(): void
    {
        $payload = $this->payload();

        $this->blockControl = new BlockControl(
            $payload['is_blocked'],
            $payload['block_date']
        );
    }

    public function internalSerializePayload(): array
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
