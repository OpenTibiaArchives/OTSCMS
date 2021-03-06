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

// gets number from URL or form
$account = new OTS_Account( (int) InputData::read('id') );

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=Account&command=update&id=' . $account->id;
$form['submit'] = $language['main.admin.UpdateSubmit'];

// form fields
$form->addField('', ComponentAdminForm::FieldLabel, $language['Modules.Account.AccountNumber'], $account->id);
$form->addField('account[email]', ComponentAdminForm::FieldText, $language['Modules.Account.EMail'], $account->eMail);
$form->addField('account[password]', ComponentAdminForm::FieldText, $language['Modules.Account.Password'], $config['system.passwords'] != 'plain' ? '' : $account->password);

$characters = array();

// reads account's characters
foreach($account as $character)
{
    $characters[] = $character->name . ' (' . $character->group->name . ')';
}

$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Account.CharactersData'];
$list['list'] = $characters;

?>
