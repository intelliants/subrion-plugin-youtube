<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    if (empty($item) || !$iaCore->get('youtube_items_enabled')) {
        return;
    }

    $enabled_items = explode(',', $iaCore->get('youtube_items_enabled'));

    if (empty($enabled_items)) {
        return;
    }

    $field = 'youtube_video';

    if (in_array($item, $enabled_items)) {
        $itemTable = $iaCore->factory('item')->getItemTable($item);
        if ($url = $iaDb->one($field, iaDb::convertIds($listing), $itemTable)) {
            if (preg_match('/^(http\:\/\/|https\:\/\/)?(www\.)?youtube\.com\/watch\?([\w=\-&]+&)?v=[\w\-]+((&|\?|#)[\w=\-&]+)?$/si', $url)) {
                $url = parse_url($url);
                parse_str($url['query'], $url);

                $iaView->assign($field, $url['v']);
            } elseif (preg_match('/^(http\:\/\/|https\:\/\/)?(www\.)?youtu\.be\/[\w\-]+((&|\?|#)[\w=\-&]+)?$/si', $url)) {
                $url = parse_url($url);
                $path = explode('/', $url['path']);

                $iaView->assign($field, $path[1]);
            } else {
                return;
            }
        }
    }
}
