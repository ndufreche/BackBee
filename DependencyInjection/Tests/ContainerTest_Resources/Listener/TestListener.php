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

namespace BackBee\DependencyInjection\Tests\ContainerTest_Resources\Listener;

use BackBee\DependencyInjection\Tests\ContainerTest;
use BackBee\Event\Event;

/**
 * This listener is part of BackBee\DependencyInjection\Tests\ContainerTest; it allows us to
 * test that BackBee\DependencyInjection\Container::get() will dispatch an event when we're
 * getting a tagged service.
 *
 * @category    BackBee
 *
 * @copyright   Lp digital system
 * @author      e.chau <eric.chau@lp-digital.fr>
 */
class TestListener
{
    /**
     * value of foo.
     *
     * @var string
     */
    private $foo;

    /**
     * TestListener's constructor;.
     */
    public function __construct()
    {
        $this->foo = 'bar';
    }

    /**
     * Getter for foo.
     *
     * @return string value of foo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * occurs on `service.tagged.test`.
     *
     * @param Event $event
     */
    public function onGetServiceTaggedTestEvent(Event $event)
    {
        $this->foo = 'foo';
        $datetime = $event->getTarget();
        $datetime->setTimestamp(ContainerTest::NEW_DATE_WITH_TAG_VALUE);
    }
}
