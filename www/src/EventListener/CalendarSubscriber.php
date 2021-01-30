<?php

namespace App\EventListener;


use CalendarBundle\Entity\Event;
use App\Repository\SessionRepository;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarListener
{
   private $router; 
   private $repo;


    public function __construct(
    SessionRepository $repo,
    UrlGeneratorInterface $router
    ) {
        $this->router = $router;
        $this->repo = $repo;

    }

    // public static function getSubscribedEvents()
    // {
    //     return [
    //         CalendarEvents::SET_DATA => 'onCalendarSetData',
    //     ];
    // }

    public function load(CalendarEvent $calendar): void
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change session.beginAt by your start date property
        // if(array_key_exists('session', $filters))
        // {
            // $repo= $sessionRepository->findAll();
            
        $sessions = $this->repo
            ->createQueryBuilder('s')
            ->where('s.startAt BETWEEN :start and :end') 
            ->orWhere('s.endAt BETWEEN :start and :end')
            ->orWhere(':end BETWEEN s.startAt and s.endAt')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult() ;
        // }
       

        foreach ($sessions as $session) {
            // this create the events with your data (here session data) to fill calendar
            $sessionEvent = new Event(
                $session->getLibelle(),
                $session->getStartAt(),
                $session->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            // $sessionEvent->setOptions([
            //     'backgroundColor' => 'red',
            //     'borderColor' => 'red',
            //     'display'=> 'grid'
            // ]);
            $sessionEvent->addOption(
                'url',
                $this->router->generate('session_show', [
                    'id' => $session->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($sessionEvent);
        }
    }
}