<?php
namespace Loans\LoanProviding\Application\Command;


use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CancelLoan extends Command
{
    use PayloadTrait;

    /**
     * LoanRequest constructor.
     * @param UuidInterface $loanId
     * @param string $reason
     */
    public function __construct(UuidInterface $loanId, string $reason)
    {
        $this->init();
        $this->setPayload([
            'loan_id' => $loanId->toString(),
            'reason' => $reason
        ]);
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function loanId()
    {
        return Uuid::fromString($this->payload['loan_id']);
    }

    /**
     * @return string
     */
    public function reason(): string
    {
        return $this->payload['reason'];
    }


}