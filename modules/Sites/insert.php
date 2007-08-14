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

// pre-loads HTTP data
$site = InputData::read('site');

// creates new guide
$row = new CMS_Site($site);

// deletes old is_home flag
if($row['is_home'])
{
    $db->exec('UPDATE [sites] SET `is_home` = 0 WHERE `is_home` = 1');
}

$row->save();


// redirects to main page
OTSCMS::call('Sites', 'list');

?>