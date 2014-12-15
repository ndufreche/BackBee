<?php

/*
 * Copyright (c) 2011-2013 Lp digital system
 *
 * This file is part of BackBuilder5.
 *
 * BackBuilder5 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BackBuilder5 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BackBuilder5. If not, see <http://www.gnu.org/licenses/>.
 */

namespace BackBuilder\ClassContent\Tests\Repository;

use BackBuilder\Site\Site;
use BackBuilder\Site\Layout;
use BackBuilder\NestedNode\Page;
use BackBuilder\ClassContent\Repository\ClassContentRepository;
use BackBuilder\ClassContent\Tests\Mock\MockContent;
use BackBuilder\Tests\TestCase;


/**
 * @category    BackBuilder
 * @package     BackBuilder\NestedNode\Tests
 * @copyright   Lp digital system
 * @author      Mickaël Andrieu <mickael.andrieu@lp-digital.fr>
 */
class ClassContentRepositoryTest extends TestCase
{
	/**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \BackBuilder\ClassContent\Repository\ClassContentRepository
     */
    private $repository;

    /**
     * @var \BackBuilder\NestedNode\Page
     */
    private $pageRoot;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
    	$this->initDb($this->getBBApp());
        $this->em = $this->getEntityManager();
        $this->repository = $this->em->getRepository('BackBuilder\ClassContent\AClassContent');

        $site = new Site('site-test', array('label' => 'site-test'));
        $this->em->persist($site);

        $layout = new Layout('layout-test', array('label' => 'layout-test', 'path' => 'layout-path'));
        $layout->setDataObject($this->getDefaultLayoutZones());
        $this->em->persist($layout);

        $this->pageRoot = new Page('root', array('title' => 'root', 'url' => 'url-root'));
        $this->pageRoot->setSite($site)
            ->setLayout($layout);

        $this->em->persist($this->pageRoot);
        $this->em->flush();

        //var_dump(\Doctrine\Common\Util\Debug::dump($this->em->getRepository('BackBuilder\ClassContent\AClassContent')->findAll()));
    }

    public function testGetSelection()
    {
        $selector = array(
                        //'parentnode' => null,
                        'orderby' => array('modified', 'desc'),
                        'limit' => 6
                    );
        $queryResult = $this->repository->getSelection($selector, false, false, 0, null, false, false, array('BackBuilder\ClassContent\ContentSet'));
        	var_dump($queryResult);
        $this->assertInstanceOf('BackBuilder\Util\Doctrine\SettablePaginator', $queryResult);
    }

    /**
     * Builds a default set of layout zones
     * @return \stdClass
     */
    private function getDefaultLayoutZones()
    {
        $mainzone = new \stdClass();
        $mainzone->id = 'main';
        $mainzone->defaultContainer = null;
        $mainzone->target = '#target';
        $mainzone->gridClassPrefix = 'row';
        $mainzone->gridSize = 8;
        $mainzone->mainZone = true;
        $mainzone->defaultClassContent = 'ContentSet';
        $mainzone->options = null;

        $asidezone = new \stdClass();
        $asidezone->id = 'aside';
        $asidezone->defaultContainer = null;
        $asidezone->target = '#target';
        $asidezone->gridClassPrefix = 'row';
        $asidezone->gridSize = 4;
        $asidezone->mainZone = false;
        $asidezone->defaultClassContent = 'inherited';
        $asidezone->options = null;

        $data = new \stdClass();
        $data->templateLayouts = array(
            $mainzone,
            $asidezone,
        );

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}