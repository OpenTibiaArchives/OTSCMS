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

$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Library.MonstersList'];

$temp = array();

// loops through monsters
foreach( $ots->getMonstersList() as $name => $monster)
{
    // checks if it's monster and admin wants to display it - to enable it place image in monsters images directory
    if( !Toolbox::imageExists('Monsters/' . $name) )
    {
        continue;
    }

    $temp[$name] = ucwords($name);
}

// puts monsters into template
$list['list'] = $temp;
$list['link'] = 'monsters/';

?>
