<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2007 Wrzasq (wrzasq@gmail.com)

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

// checks characters limit
$account = $ots->createObject('Account');
$account->load(User::$number);
if( count( $account->getPlayers() ) >= $config['system.account_limit'])
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Character.Limit'];
    return;
}

// character creation form
$form = $template->createComponent('AdminForm');
$form['action'] = '/characters/insert';
$form['submit'] = $language['Modules.Character.InsertSubmit'];

$form->addField('character[name]', ComponentAdminForm::FieldText, $language['Modules.Character.Name']);
$form->addField('character[sex]', ComponentAdminForm::FieldRadio, $language['Modules.Character.Gender'], array('options' => array($language['main.gender0'], $language['main.gender1']) ) );
$form->addField('character[vocation]', ComponentAdminForm::FieldRadio, $language['Modules.Character.Vocation'], array('options' => array($language['main.vocation0'], $language['main.vocation1'], $language['main.vocation2'], $language['main.vocation3'], $language['main.vocation4']) ) );

// if rook is on then there is no need for city selection
if(!$config['system.rook.enabled'])
{
    $towns = array();

    // reads all spawns
    foreach( new SpawnsReader($config['directories.data'] . 'world/' . $config['system.map']) as $id => $town)
    {
        $towns[$id] = $town;
    }

    $form->addField('character[town]', ComponentAdminForm::FieldSelect, $language['Modules.Character.City'], array('options' => $towns) );
}

?>
