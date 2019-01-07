<?php
/**
 * Created by PhpStorm.
 * User: adrianchlubek
 * Date: 04/01/2019
 * Time: 11:55
 */

namespace App\Services;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class RatchetServer implements MessageComponentInterface
{

    protected $clients;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    const ON_CONNECT_EVENT = 'websocket.connect.event';
    const ON_MESSAGE_EVENT = 'websocket.message.event';
    const ON_DISCONNECT_EVENT = 'websocket.disconnect.event';
    const ON_ERROR_EVENT = 'websocket.error.event';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function start($port)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this
                )
            ),
            $port
        );

        $server->run();
    }

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager) {
        $this->clients = new \SplObjectStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $animal = new Animal();
        $animal->setName($msg);
        $this->entityManager->persist($animal);
        $this->entityManager->flush();
       /* $repo = $this->entityManager->getRepository(Animal::class);
        $animals = $repo->findAll();

        foreach ($this->clients as $client) {
            //if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
            foreach ($animals as $a) {
                $client->send($a->getName());
            }
            //}
        }*/
        gc_collect_cycles();
        memory_get_usage();
        memory_get_usage(true);
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
