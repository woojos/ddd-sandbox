<?php

use Loans\LoanProviding\Application\Command\LoanRequest;
use Loans\LoanProviding\Application\LoanProvidingService;
use Loans\LoanProviding\Domain\Event\LoanCreated;
use Loans\LoanProviding\Domain\LoanRequestVerifierFactory;
use Money\Money;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/setup.php';

$eventRouter
    ->route(LoanCreated::class)
    ->to(function(LoanCreated $event){
        //var_dump($event);
    });

$service = new LoanProvidingService(
    $eventBasedLoanRepo,
    $eventBus,
    new LoanRequestVerifierFactory()
);

$commandRouter
    ->route(LoanRequest::class)
    ->to(function (LoanRequest $command) use ($service) {
        $service->requestForLoan($command);
    });

$loanId = Uuid::uuid4();
$customerId = Uuid::uuid4();
$totalAmount = Money::EUR(2000);
$dateTo = new DateTimeImmutable();


$loan = $eventBasedLoanRepo->get(UUid::fromString('3ee99774-5cb1-48a0-a9b1-30d87c919ec9'));
echo $loan->getRemainingAmount()->getAmount();

/*
$commandBus->dispatch(new LoanRequest(
    $loanId, $customerId, $totalAmount, $dateTo
));*/
/*
$loan = $eventBasedLoanRepo->get(UUid::fromString('3ee99774-5cb1-48a0-a9b1-30d87c919ec9'));
for ($i = 0; $i < 100; $i++) {
    $loan->payoff(Money::EUR(1));
}
*/

//$loan->save();

//$eventBasedLoanRepo->save($loan);

//$loan->cancel('adas');
//print_r($loan->getRemainingAmount());