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

// loads poll option (poll id will be used for redirection)
$option = new CMS_Option( (int) InputData::read('id') );

// checks if user voted in current poll
if( Toolbox::haveVoted($option['poll']) )
{
    // user already voted in this poll
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Poll.AlreadyVoted'];
}
else
{
    // saves new vote
    $db->query('INSERT INTO [votes] (`name`, `content`) VALUES (' . $option['id'] . ', ' . User::$number . ')');

    // redirects to poll page
    InputData::write('id', $option['poll']);
    OTSCMS::call('Poll', 'display');
}

?>
