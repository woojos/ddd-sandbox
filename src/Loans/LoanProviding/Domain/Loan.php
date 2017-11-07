<?php
namespace Loans\LoanProviding\Domain;

use Loans\LoanProviding\Domain\Event\LoanCanceled;
use Loans\LoanProviding\Domain\Event\LoanCreated;
use Loans\LoanProviding\Domain\Event\LoanPaidOff;
use Money\Money;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\UuidInterface;

class Loan extends AggregateRoot
{
    /** @var UuidInterface */
    private $id;
    /** @var UuidInterface */
    private $customerId;
    /** @var Money */
    private $totalAmount;
    /** @var Money */
    private $remainingAmount;
    /** @var \DateTimeImmutable */
    private $dateTo;
    /** @var LoanStatus */
    private $status;

    /**
     * @param UuidInterface $id
     * @param UuidInterface $customerId
     * @param Money $totalAmount
     * @param \DateTimeImmutable $dateTo
     */
    public static function init(UuidInterface $id, UuidInterface $customerId, Money $totalAmount, \DateTimeImmutable $dateTo)
    {
        $loan = new self();
        $loan->recordThat(
            LoanCreated::create($id, $customerId, $totalAmount, $dateTo)
        );

        return $loan;
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case LoanCreated::class:
                /** @var LoanCreated $event */
                $this->id = $event->id();
                $this->customerId = $event->customerId();
                $this->totalAmount = $event->totalAmount();
                $this->remainingAmount = $event->totalAmount();
                $this->dateTo = $event->dateTo();
                $this->status = LoanStatus::ACTIVE();
                break;
            case LoanPaidOff::class:
                /** @var LoanPaidOff $event */
                $this->remainingAmount = $this->remainingAmount->subtract($event->money());
                break;
            case LoanCanceled::class:
                $this->status = LoanStatus::CANCELED();
                break;
        }
    }


    public function payoff(Money $money): void
    {
        if (!$this->canBePayoff()) {
            throw new \LogicException();
        }

        //if ($money->lessThanOrEqual($this->remainingAmount)) {
            $this->recordThat(
                LoanPaidOff::create($this->id, $money)
            );
        /*
        } else {


            $this->recordThat(
                LoanFullyPaid::create($this->id, $money)
            );

            $this->recordThat(
                LoanOverPaid::create($this->id, $money->subtract($this->remainingAmount));
            );
        } */

    }

    public function cancel(string $reason): void
    {
        if (!$this->canBeCanceled()) {
            throw new \LogicException();
        }

        $this->recordThat(
            LoanCanceled::create($this->id, $reason)
        );
    }

    /**
     * @return UuidInterface
     */
    public function getId():UuidInterface
    {
        return $this->id;
    }

    /**
     * @return UuidInterface
     */
    public function getCustomerId(): UuidInterface
    {
        return $this->customerId;
    }

    /**
     * @return Money
     */
    public function getTotalAmount(): Money
    {
        return $this->totalAmount;
    }

    /**
     * @return Money
     */
    public function getRemainingAmount(): Money
    {
        return $this->remainingAmount;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateTo(): \DateTimeImmutable
    {
        return $this->dateTo;
    }

    /**
     * @return LoanStatus
     */
    public function getStatus(): LoanStatus
    {
        return $this->status;
    }

    public function canBeCanceled(): bool
    {
        return $this->status == LoanStatus::ACTIVE();
    }

    public function canBePayoff(): bool
    {
        return $this->status == LoanStatus::ACTIVE();
    }
}