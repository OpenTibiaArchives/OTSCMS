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

// profile selection form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=Character&command=settings';
$form['submit'] = $language['Modules.Character.SelectSubmit'];

$form->addField('gender', ComponentAdminForm::FieldSelect, $language['Modules.Character.SelectGender'], array('options' => array('*' => '*', 0 => $language['main.gender0'], 1 => $language['main.gender1']) ) );
$form->addField('vocation', ComponentAdminForm::FieldSelect, $language['Modules.Character.SelectVocation'], array('options' => array_merge( array('*' => '*'), $ots->getVocationsList()->getIterator()->getArrayCopy() ) ) );
$form->addField('', ComponentAdminForm::FieldSeparator, XMLToolbox::inparse($language['Modules.Character.SelectHelp']) );

?>
