# symfony-messenger-sqlite
## A simple example showing how to initialize and to use a Symfony Messenger with queues in SQLite database without Doctrine.

It is modified example from https://github.com/balpom/symfony-messenger-sample, which use Doctrine  ((https://github.com/symfony/doctrine-messenger)) as message transport.
This example use https://github.com/balpom/sql-messenger as transport. It use SQLite database by PDO and without Doctrine.
Everything was tested in Linux.

### Requirements 
- **PHP >= 8.1**

### Installation
#### Using composer (recommended)
```bash
composer create balpom/symfony-messenger-sqlite
```

## How to use

### Simple test
Open console window. Run the command:
```bash
php bin/console messenger:consume sql-async
```
It starts simple Worker, which imitate SMS sending. Now it is waiting for messages to be sent from the queue, which is still empty.

Open another console window. Run the command:
```bash
php tests/send.php
```
It runs a simple script that adds several messages to the queue.
After this, in first console window you may see, how Worker "sending" SMS.

Run the command:
```bash
php bin/console messenger:stop-workers
```
It stop Worker execution.

### Advanced test
Open multiple consoles. In each of them, run the command:
```bash
php bin/console messenger:consume doctrine-async
```
It starts many simple Workers, which imitate SMS sending. Now it is waiting for messages to be sent from the queue, which is still empty.

Open one more console window. Run the command:
```bash
php tests/sendmany.php
```
It runs a simple script that adds many several messages to the queue.
After this, in previously opened consoles you may see, how several Workers "sending" SMS.

Run the command:
```bash
php bin/console messenger:stop-workers
```
It stop all Workers executions.

