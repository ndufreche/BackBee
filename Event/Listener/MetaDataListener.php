<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of BackBee.
 *
 * BackBee is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BackBee is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BackBee. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 */

namespace BackBee\Event\Listener;

use BackBee\ClassContent\AbstractClassContent;
use BackBee\Event\Event;
use BackBee\NestedNode\Page;

/**
 * Listener to metadata events.
 *
 * @category    BackBee
 *
 * @copyright   Lp digital system
 * @author      c.rouillon <charles.rouillon@lp-digital.fr>
 */
class MetaDataListener
{
    private static $onFlushPageAlreadyCalled = false;

    /**
     * Occur on classcontent.onflush events.
     *
     * @param \BackBee\Event\Event $event
     */
    public static function onFlushContent(Event $event)
    {
        $content = $event->getTarget();
        if (!($content instanceof AbstractClassContent)) {
            return;
        }

        $application = $event->getApplication();
        $em = $application->getEntityManager();
        $uow = $em->getUnitOfWork();
        if ($uow->isScheduledForDelete($content)) {
            return;
        }

        if (null !== $page = $content->getMainNode()) {
            if (null !== $page->getMetaData()) {
                $newEvent = new Event($page, $content);
                $newEvent->setDispatcher($event->getDispatcher());
                self::onFlushPage($newEvent);
            }
        }
    }

    /**
     * Occur on nestednode.page.onflush events.
     *
     * @param \BackBee\Event\Event $event
     */
    public static function onFlushPage(Event $event)
    {
        if (true === self::$onFlushPageAlreadyCalled) {
            return;
        }

        $page = $event->getTarget();
        if (!($page instanceof Page)) {
            return;
        }

        $application = $event->getApplication();
        $em = $application->getEntityManager();
        $uow = $em->getUnitOfWork();
        if ($uow->isScheduledForDelete($page)) {
            return;
        }

        if (null === $metadata_config = $application->getConfig()->getSection('metadata')) {
            return;
        }

        if (null === $metadata = $page->getMetaData()) {
            $metadata = new \BackBee\MetaData\MetaDataBag($metadata_config, $page);
        } else {
            $metadata->update($metadata_config, $page);
        }
        $page->setMetaData($metadata->compute($page));

        if ($uow->isScheduledForInsert($page) || $uow->isScheduledForUpdate($page)) {
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata('BackBee\NestedNode\Page'), $page);
        } elseif (!$uow->isScheduledForDelete($page)) {
            $uow->computeChangeSet($em->getClassMetadata('BackBee\NestedNode\Page'), $page);
        }

        self::$onFlushPageAlreadyCalled = true;
    }
}
