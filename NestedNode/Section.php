<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of BackBee.
 *
 * BackBee5 is free software: you can redistribute it and/or modify
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

namespace BackBee\NestedNode;

use Doctrine\Common\Collections\ArrayCollection;

use BackBee\Site\Site;

/**
 * Section object in BackBee
 * @category    BackBee
 * @package     BackBee\NestedNode
 * @author      Micael Malta <mmalta@nextinteractive.fr>
 * @Entity(repositoryClass="BackBee\NestedNode\Repository\SectionRepository")
 * @Table(name="section",indexes={@index(columns={"uid", "root_uid", "leftnode", "rightnode"})})
 * @HasLifecycleCallbacks
 */
class Section extends ANestedNode
{
    /**
     * Unique identifier of the section
     * @var string
     * @Id @Column(type="string", name="uid", nullable=false)
     */
    protected $_uid;

    /**
     * The root node, cannot be NULL.
     * @var \BackBee\NestedNode\Section
     * @ManyToOne(targetEntity="BackBee\NestedNode\Section", inversedBy="_descendants", fetch="EXTRA_LAZY")
     * @JoinColumn(name="root_uid", referencedColumnName="uid")
     */
    protected $_root;

    /**
     * The parent node.
     * @var \BackBee\NestedNode\Section
     * @ManyToOne(targetEntity="BackBee\NestedNode\Section", inversedBy="_children", fetch="EXTRA_LAZY")
     * @JoinColumn(name="parent_uid", referencedColumnName="uid", nullable=true)
     */
    protected $_parent;

    /**
     * Descendants nodes.
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="BackBee\NestedNode\Section", mappedBy="_root", fetch="EXTRA_LAZY")
     */
    protected $_descendants;

    /**
     * Direct children nodes.
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="BackBee\NestedNode\Section", mappedBy="_parent", fetch="EXTRA_LAZY")
     */
    protected $_children;

    /**
     * The associated page of this section
     * @var \BackBee\NestedNode\Page
     * @OneToOne(targetEntity="BackBee\NestedNode\Page", fetch="EXTRA_LAZY")
     * @JoinColumn(name="page_uid", referencedColumnName="uid")
     */
    protected $_page;

    /**
     * Store pages using this section.
     * var \Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="BackBee\NestedNode\Page", mappedBy="_section", fetch="EXTRA_LAZY")
     */
    protected $_pages;

    /**
     * The owner site of this section
     * @var \BackBee\Site\Site
     * @ManyToOne(targetEntity="BackBee\Site\Site", fetch="EXTRA_LAZY")
     * @JoinColumn(name="site_uid", referencedColumnName="uid", nullable=false)
     */
    protected $_site;

    /**
     * Class constructor
     * @param string $uid     The unique identifier of the section
     * @param array  $options Initial options for the section:
     *                        - page      the associated page
     *                        - site      the owning site
     */
    public function __construct($uid = null, $options = null)
    {
        parent::__construct($uid, $options);

        if (
                true === is_array($options) &&
                true === array_key_exists('page', $options) &&
                $options['page'] instanceof Page
        ) {
            $this->setPage($options['page']);
        }

        if (
                true === is_array($options) &&
                true === array_key_exists('site', $options) &&
                $options['site'] instanceof Site
        ) {
            $this->setSite($options['site']);
        }

        $this->_pages = new ArrayCollection();
    }

    /**
     * Magical cloning method
     */
    public function __clone()
    {
        $this->_uid = md5(uniqid('', true));
        $this->_leftnode = 1;
        $this->_rightnode = $this->_leftnode + 1;
        $this->_level = 0;
        $this->_created = new \DateTime();
        $this->_modified = new \DateTime();
        $this->_parent = null;
        $this->_root = $this;

        $this->_children = new ArrayCollection();
        $this->_descendants = new ArrayCollection();
        $this->_pages = new ArrayCollection();
    }

    /**
     * Sets the associated page for this section
     * @param  \BackBee\NestedNode\Page    $page
     * @return \BackBee\NestedNode\Section
     */
    public function setPage(Page $page)
    {
        $this->_page = $page;
        $page->setMainSection($this);

        return $this;
    }

    /**
     * Returns the associated page this section
     * @return \BackBee\NestedNode\Page
     * @codeCoverageIgnore
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Returns the owning pages
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @codeCoverageIgnore
     */
    public function getPages()
    {
        return $this->_pages;
    }

    /**
     * Sets the site of this section
     * @param  \BackBee\Site\Site          $site
     * @return \BackBee\NestedNode\Section
     */
    public function setSite(Site $site = null)
    {
        $this->_site = $site;

        return $this;
    }

    /**
     * Returns the site of this section
     * @return \BackBee\Site\Site
     * @codeCoverageIgnore
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * Returns an array representation of the node
     * @return string
     */
    public function toArray()
    {
        $result = parent::toArray();
        $result['siteuid'] = (null !== $this->getSite()) ? $this->getSite()->getUid() : null;

        return $result;
    }

    /**
     * A section is never a leaf
     * @return Boolean always FALSE
     */
    public function isLeaf()
    {
        return false;
    }
}
