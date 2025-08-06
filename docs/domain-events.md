# Domain Events

## Definitions

The essence of a Domain Event is that you use it to capture things that can trigger a change to the state of the application you are developing. These event objects are then processed to cause changes to the system, and stored to provide an Audit Log.

A Domain Event captures the memory of something interesting which affects the domain — Martin Fowler

A domain event is, something that happened in the domain that you want other parts of the same domain (in-process) to be aware of. The notified parts usually react somehow to the events.

An important benefit of domain events is that side effects can be expressed explicitly.

An event is something that has happened in the past, that's why **we should use past tense to define them**

## Implementation I - Create a Domain Event

A domain event is something that happened to one of our entities that required a change of state.

Imagine an entity /aggregate like this:

```php
<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Events\DomainEventPublisher;

class Author
{
    /**
     * @param int    $id    Ex: 1
     * @param string $email Ex: john.smith@example.com
     * @param string $name  Ex: JOhn Smith
     */
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
    ) {}
```

Then we can create an event when the Author has been created, like this:

```php
<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Events\DomainEventPublisher;

class Author
{
    ...

    public static function create(string $nextId, string $name, string $email)
    {
        DomainEventPublisher::instance()
            ->publish(
                new AuthorCreatedEvent(static::class, $nextId, $name, $email)
            )
        ;

        return new Author($nextId, $name, $email);
    }
}
```

The trick here is to call the static singleton:
DomainEventPublished::instance()

The events must be classes that implements the interface of our domain events: src/Domain/Events/DomainEventInterface
Please create the class with a past tense verb inside this folder src/Domain/Events, for example:

```php
<?php

declare(strict_types=1);

namespace App\Domain\Events;

final readonly class CreatedAuthorEvent implements DomainEventInterface
{
    public function __construct(public string $class, public string $id, public string $name, public string $email) {}

    public function getClass(): string
    {
        return $this->class;
    }
}
```

It's important to save all the possible data that it's important for this event.
This is why we save the name of the entity /aggregate that launch this event, with a class fqnm name, and its required for all domain events.
(Later we want to filter by entity, so this class will be persisted in the database for later execute filters)
Other relevant data, for example if an author was created, probably we want to save things like name and the email, etc...

Internally we call these events in the command Bus, in the commandHandler middleware, so they get dispatched and saved on :
src\Infrastructure\Bus\CommandMiddleWares\CommandHandlerSelectorMiddleware.php

So our command handler will look like this:

```php
    /**
     * @param CreateAuthorCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $author = Author::create(
            $command->authorDTO->id ?? Uuid::v7()->toRfc4122(),
            $command->authorDTO->name,
            $command->authorDTO->email
        );

        $this->authorRepository->save($author);
    }
```

***Its important our handlers or use cases, respect the domain language, or ubiquitous language. If the domain says and author can be created, we need to call Author:create
and the domain something like AuthorCreated (please respect entity-pasttenseverb). If they domain says the author gets imported , should be Author::import in the handler, and AuthorImported.***

Since the Id, can be a valid uuid from the frontend or in case its empty we need to create one, we use uuid v7:
$command->authorDTO->id ?? Uuid::v7()->toRfc4122(),

## Implementation II - Create a subscriber

One interesting thing to having the domain modeled with domain events, is, we can trigger other actions sync or async.
Our current implementation is quite simple and sync, but can be easily moved to an async using queues or other options.

So far, to react to this event after it happened, we just need to create a subscriber in the application layer,
for example:
src/Application/Subscribers/SendEmailWhenPetCreated.php

```php
<?php

declare(strict_types=1);

namespace App\Application\Subscribers;

use App\Domain\Events\PetCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendEmailWhenPetCreated implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [PetCreated::class => 'onPetCreated'];
    }

    public function onPetCreated(PetCreated $event)
    {
       // implement email on infra etc...
    }
}
```

If you realize we used the [eventdispatcher](https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber) from symfony, but we can easily have it changed in a future it to a bus using symfony messenger like in this example:
[https://medium.com/@albertcolom/how-to-publish-domain-events-with-doctrine-listener-f48a8a18681d](https://medium.com/@albertcolom/how-to-publish-domain-events-with-doctrine-listener-f48a8a18681d)
