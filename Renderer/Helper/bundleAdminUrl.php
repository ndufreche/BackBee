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

namespace BackBee\Renderer\Helper;

use BackBee\Bundle\BundleControllerResolver;

/**
 * @category    BackBee
 *
 * @author      Nicolas Dufreche <nicolas.dufreche@lp-digital.fr>
 */
class bundleAdminUrl extends AbstractHelper
{

    /**
     * @param  string   $route      route is composed by the bundle, controller and action name separated by a dot
     * @param  array    $parameters optional parameters
     * @param  array    $query      optional query parameters
     *
     * @return string               url
     */
    public function __invoke($route, Array $parameters = [], Array $query = [])
    {
        list($bundle, $controller, $action) = split('.', $route);

        if ($this->_renderer->getApplication()->isDebugMode()) {
            $this->checkParameters($bundle, $controller, $action);
        }

        $container = $this->_renderer->getApplication()->getContainer();

        $url = $container->get('bbapp.rest_api.path').$container->get('bbapp.rest_api.version').'/bundle/'.$bundle.'/'.$controller.'/'.$action;

        if (count($parameters) !== 0) {
            $url = $url.'/'.implode('/', $parameters);
        }

        if (count($query) !== 0) {
            $url = $url.'?'.implode('&', $query);
        }

        return $this->_renderer->getApplication()->getRouting()->getUri($url);
    }

    /**
     * Check if each parameters are correctly setted
     * @param  string $bundle       bundle name
     * @param  string $controller   controller name
     * @param  string $action       action name
     *
     * @throws Exception            Bad configuration
     */
    private function checkParameters($bundle, $controller, $action)
    {
        $container = $this->_renderer->getApplication()->getContainer();

        $bundleController = (new BundleControllerResolver($container))->resolve($bundle, $controller);

        if (!method_exists($bundleController, $action.'Action')) {
            throw new \BadMethodCallException($bundleController.' doesn\'t have '.$action.'Action method', 1);
        }
    }
}
