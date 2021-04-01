<?php

namespace App\EventListener;

use App\Entity\Channel;
use DateTime;
use App\Entity\Log;
use App\Entity\Program;
use ReflectionClass;
use App\Entity\Video;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class LogSubscriber implements EventSubscriber
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
            Events::postLoad,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logActivity('persist', $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->logActivity('remove', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logActivity('update', $args);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Log) {

            $logEntity = $this->entityManager->getRepository($entity->getEntityName())->find($entity->getEntityId());
            $entity->setEntity($logEntity);
        }
    }

    private function logActivity(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (($entity instanceof Video || $entity instanceof Program ||$entity instanceof Channel)
            && preg_match('#^ws#',$route)) {
            $rc = new ReflectionClass(get_class($entity)); 
            $log = new Log();
            $log->setCreatedAt(new DateTime())
                ->setRoute($route)
                ->setEntityName($rc->getName())
                ->setEntityId($entity->getId())
                ;
            if ($entity instanceof Video) {
                $log->setStatus($entity->getStatus());
            }
            $this->entityManager->persist($log);
            $this->entityManager->flush();
        }

        // ... get the entity information and log it somehow
    }
}