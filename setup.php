<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Loans\LoanProviding\Domain\Loan;
use Loans\LoanProviding\Infrastructure\EventBasedLoanRepository;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MySqlSingleStreamStrategy;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;

require_once __DIR__ . '/vendor/autoload.php';

$config = new Configuration();

$connectionParams = [
    'dbname' => 'loans',
    'user' => 'root',
    'password' => 'root',
    'host' => '127.0.0.1',
    'port' => '6603',
    'driver' => 'pdo_mysql'
];

$connection = DriverManager::getConnection($connectionParams);

try {
    $connection->prepare(file_get_contents(__DIR__ . '/vendor/prooph/pdo-event-store/scripts/mysql/01_event_streams_table.sql'))->execute();
    $connection->prepare(file_get_contents(__DIR__ . '/vendor/prooph/pdo-event-store/scripts/mysql/02_projections_table.sql'))->execute();
    $connection->prepare(file_get_contents(__DIR__ . '/scripts/read_model_loans.sql'))->execute();
    $connection->prepare(file_get_contents(__DIR__ . '/vendor/prooph/pdo-snapshot-store/scripts/mysql_snapshot_table.sql'))->execute();
} catch (\Exception $e) {
    //echo $e->getMessage();
}

$eventBus = new EventBus();
$eventRouter = new EventRouter();
$eventPublisher = new EventPublisher($eventBus);
$eventRouter->attachToMessageBus($eventBus);

$commandBus = new CommandBus();
$commandRouter = new CommandRouter();
$commandRouter->attachToMessageBus($commandBus);

$eventStore =
    new ActionEventEmitterEventStore(
        new MySqlEventStore(
            new FQCNMessageFactory(),
            $connection->getWrappedConnection(),
            new MySqlSingleStreamStrategy()
        ),
        new ProophActionEventEmitter()
);

$eventPublisher->attachToEventStore($eventStore);

$streamName = new StreamName('event_stream');
$singleStream = new Stream($streamName, new ArrayIterator());

if (!$eventStore->hasStream($streamName)) {
    $eventStore->create($singleStream);
}

$snapshotStore = new PdoSnapshotStore($connection->getWrappedConnection());

$aggregateRepository = new AggregateRepository(
    $eventStore,
    AggregateType::fromAggregateRootClass(Loan::class),
    new AggregateTranslator(),
    $snapshotStore
);

$eventBasedLoanRepo = new EventBasedLoanRepository($aggregateRepository);

$projectionManager = new MySqlProjectionManager(
    $eventStore,
    $connection->getWrappedConnection()
);