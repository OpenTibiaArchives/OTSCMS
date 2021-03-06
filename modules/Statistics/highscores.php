<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2008 Wrzasq (wrzasq@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$template->addJavaScript('statistics');

// HTTP variables
$list = InputData::read('list');
$page = InputData::read('page');

// checks if the given mode is the valid list type
if( !in_array($list, array('experience', 'maglevel', 'shielding', 'distance', 'sword', 'club', 'axe', 'fist', 'fishing') ) )
{
    $list = 'experience';
}

// skills statistics
if( in_array($list, array('shielding', 'distance', 'sword', 'club', 'axe', 'fist', 'fishing') ) )
{
    OTSCMS::call('Statistics', 'skills');
    return;
}

$limit = $config['statistics.page'];

// reads count of all reocrds
$pages = $db->query('SELECT COUNT({players}.`id`) AS `count` FROM {players}, {groups} WHERE {players}.`group_id` = {groups}.`id` AND {groups}.`access` < 3')->fetch();
$pages = ceil($pages['count'] / $limit);

// checks if the site is valid
$page = $page < 0 ? 0 : ($page > $pages - 1 ? $pages - 1 : $page);

$pager = $template->createComponent('StatisticsPager');
$pager['list'] = $list;
$pager['page'] = $page;

// generates links
$pager['left'] = array('show' => $page > 0, 'from' => ($page - 1) * $limit + 1, 'to' => $page * $limit);
$pager['right'] = array('show' => $page < $pages - 1, 'from' => ($page + 1) * $limit + 1, 'to' => ($page + 2) * $limit);

$scores = array();

// reads top scores from given range
$i = $page * $limit;

// experience statistics excluding gamemasters
foreach( $db->query('SELECT {players}.`name` AS `name`, ' . ($list == 'experience' ? '{players}.`experience` AS `value`, {players}.`level` AS `level`' : '{players}.`maglevel` AS `value`') . ' FROM {players}, {groups} WHERE {players}.`group_id` = {groups}.`id` AND {groups}.`access` < 3 ORDER BY {players}.`' . $list . '` DESC LIMIT ' . $limit . ' OFFSET ' . ($page * $limit) ) as $row)
{
    $scores[++$i] = $row;
}

$pager['scores'] = $scores;

?>
