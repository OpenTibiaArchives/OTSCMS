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

/*
    Session handler.
*/

class Session
{
    // prefix for names
    private static $prefix;

    // starts session
    // sets prefix for session variables
    public static function init($prefix)
    {
        self::$prefix = $prefix;
        session_start();

        // continues prevention
        if( ini_get('register_globals') )
        {
            _compat_revert_register_globals($_SESSION);
        }
    }

    // clears session variable
    public static function unRegister()
    {
        foreach( func_get_args() as $name)
        {
            unset($_SESSION[self::$prefix . $name]);
        }
    }

    // returns session variable
    public static function read($name)
    {
        return isset($_SESSION[self::$prefix . $name]) ? $_SESSION[self::$prefix . $name] : null;
    }

    // writes session variable
    public static function write($name, $value)
    {
        $_SESSION[self::$prefix . $name] = $value;
    }
}

// startup initialization
$config = OTSCMS::getResource('Config');
Session::init($config['cookies.prefix']);

?>
