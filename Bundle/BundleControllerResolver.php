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

namespace BackBee\Bundle;

use BackBee\Bundle\Exception\BundleConfigurationException;
use BackBee\DependencyInjection\ContainerInterface;

/**
 * @category    BackBee
 *
 * @author      Nicolas Dufreche <nicolas.dufreche@lp-digital.fr>
 */
class BundleControllerResolver
{
    private $container;

    public function __construct(ContainerInterface $application)
    {
        $this->container = $container;
    }

    private function computeBundleName($name)
    {
        return str_replace('%bundle_name%', strtolower($name), BundleInterface::BUNDLE_SERVICE_ID_PATTERN);
    }

    /**
     * Check if each parameters are correctly setted
     *
     * @param  string $bundle       bundle name
     * @param  string $controller   controller name
     * @param  string $action       action name
     *
     * @throws Exception            Bad configuration
     */
    private function resolve($bundle, $controller)
    {
        if (!$this->container->has($this->computeBundleName($bundle))) {
            throw new BundleConfigurationException($bundle.' doesn\'t exists', BundleConfigurationException::BUNDLE_UNDLECLARED);
        }

        $config = $this->container->get($this->computeBundleName($bundle));

        if (!isset($config['controller'])) {
            throw new BundleConfigurationException('No controller definition in '.$bundle.' bundle configuration', BundleConfigurationException::CONTROLLER_SECTION_MISSING);
        }

        if (!isset($config['controller'][$controller])) {
            throw new BundleConfigurationException($controller.' controller is undefinned in '.$bundle.' bundle configuration', BundleConfigurationException::CONTROLLER_UNDLECLARED);
        }

        return new {'\\'.$config['controller']['$controller']}($this->_renderer->getApplication());
    }
}