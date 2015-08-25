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

use BackBee\Renderer\Renderer;

/**
 * @author Eric Chau <eric.chau@lp-digital.fr>
 * @author Nicolas Dufreche <nicolas.dufreche@lp-digital.fr>
 */
class BundleAdminHelper
{
    const UPLOAD_ORIGINAL_NAME_PATTERN = '%s-original-name';
    const UPLOAD_PATH_PATTERN = '%s-path';
    const UPLOAD_FILE_NAME_PATTERN = '%s';

    /**
     * @var \BackBee\Renderer\Renderer
     */
    protected $renderer;


    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function url($route, Array $query = [])
    {
        return $this->renderer->bundleAdminUrl($route, $query);
    }

    public function link($route, Array $query = [], $httpMethod = 'GET')
    {
        return $this->renderer->bundleAdminLink($route, $query, $httpMethod);
    }

    public function form($route, Array $query = [], $httpMethod = 'POST')
    {
        return $this->renderer->bundleAdminForm($route, $query, $httpMethod);
    }

    public function fileUpload($name)
    {
        return '<input name="'.md5($name).'" data-file-upload="'.$name.'" type="file"'.$input.'>'.
            '<input id="'.sprintf(self::UPLOAD_ORIGINAL_NAME_PATTERN, $name).'" name="'.sprintf(self::UPLOAD_ORIGINAL_NAME_PATTERN, $name).'" type="hidden" value="">'.
            '<input id="'.sprintf(self::UPLOAD_PATH_PATTERN, $name).'" name="'.sprintf(self::UPLOAD_PATH_PATTERN, $name).'" type="hidden" value="">'.
            '<input id="'.sprintf(self::UPLOAD_FILE_NAME_PATTERN, $name).'" name="'.sprintf(self::UPLOAD_FILE_PATTERN, $name).'" type="hidden" value="">';
    }

}
