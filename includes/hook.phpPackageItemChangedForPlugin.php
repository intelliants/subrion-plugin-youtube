<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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
    if (empty($data)) {
        return;
    }

    $plugin = 'youtube';
    $field = 'youtube_video';
    $pages = [
        'members' => ['view_member', 'profile'],
        'autos' => ['autos_add', 'autos_edit'],
        'autos_parts' => ['autos_part_add', 'autos_part_edit'],
        'autos_services' => ['autos_service_add', 'autos_service_edit'],
        'articles' => ['submit_article', 'edit_article'],
        'estates' => ['estate_submit', 'estate_edit'],
        'listings' => ['add_listing', 'edit_listing'],
    ];

    foreach ($data as $node) {
        $item = $node['item'];
        $langKey = 'field_' . $item . '_youtube_video';
        switch ($node['action']) {
            case '+':
                if ($iaDb->exists('`item` = :item AND `name` = :field_name', ['item' => $item, 'field_name' => $field], 'fields')) {
                    $iaDb->update(['status' => iaCore::STATUS_ACTIVE], "`name` = '$field' AND `item` = '$item'", null, 'fields');
                } else {
                    $itemTable = $iaCore->factory('item')->getItemTable($item);
                    $sql = sprintf("ALTER TABLE `%s%s` ADD `%s` VARCHAR(%d) NOT NULL DEFAULT ''", $iaCore->iaDb->prefix, $itemTable, $field, 128);
                    $iaDb->query($sql);

                    $id = $iaDb->insert([
                        'module' => $plugin,
                        'item' => $item,
                        'name' => $field,
                        'type' => 'text',
                        'length' => 128,
                        'status' => iaCore::STATUS_ACTIVE
                    ], false, 'fields');
                    if (isset($pages[$item])) {
                        $rows = [];
                        foreach ($pages[$item] as $page) {
                            $rows[] = [
                                'page_name' => $page,
                                'field_id' => $id
                            ];
                        }
                        $iaDb->insert($rows, false, 'fields_pages');
                    }

                    iaLanguage::addPhrase($langKey, iaLanguage::get('youtube_video'), '', 'youtube');
                }
                break;

            case '-':
                $iaDb->update(['status' => iaCore::STATUS_APPROVAL], "`name` = '{$field}' && `item` = '{$item}'", null, 'fields');
        }
    }
}
