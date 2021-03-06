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

// guilds list
$guilds = array();

foreach( new OTS_Guilds_List() as $guild)
{
    $div = XMLToolbox::createElement('div');

    // guild emblemat
    $icon = $guild->getCustomField('icon');

    if( !empty($icon) )
    {
        $a = XMLToolbox::createElement('a');
        $img = XMLToolbox::createElement('img');
        $img->setAttribute('src', $icon);
        $a->setAttribute('href', 'guild/' . $guild->id);
        $a->addContent($img);
        $div->addContent($a);
    }

    // label link
    $a = XMLToolbox::createElement('a');
    $a->setAttribute('href', 'guild/' . $guild->id);
    $a->addContent($guild->name);
    $div->addContent($a);

    // guild text
    $p = XMLToolbox::createElement('p');
    $p->addContent( $guild->getCustomField('content') );
    $div->addContent($p);

    $guilds[$guild->id] = $div;
}

// news display component
$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Guilds.GuildsList'];
$list['list'] = $guilds;

// archive link
$link = $template->createComponent('Links');
$link['links'] = array( array('label' => $language['Modules.Guilds.CreateSubmit'], 'link' => 'guild/create') );

?>
