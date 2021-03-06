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

$email = InputData::read('email');

// validates e-mail
if( !preg_match('/^[a-z][\w\.+-]*[a-z0-9]@[a-z0-9][\w\.+-]*\.[a-z][a-z\.]*[a-z]$/i', $email) )
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Account.PleaseEMail'];
    OTSCMS::call('Account', 'signup');
    return;
}

$account = new OTS_Account($email);

// checks if this e-mail was already used
if($account->loaded)
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Account.AlreadyUsed'];
    OTSCMS::call('Account', 'signup');
    return;
}

// generates random account number
try
{
    $number = $account->create($config['system.min_number'], $config['system.max_number']);
}
catch(E_OTS_Generic $e)
{
    // no free numbers
    if( $e->getCode() == E_OTS_Generic::CREATE_ACCOUNT_IMPOSSIBLE)
    {
        throw new HandledException('OutOfNumbers');
    }
    // we don't know what is it at the moment
    else
    {
        throw $e;
    }
}

// generates random password
$password = substr( md5( uniqid( rand(), true) ), 1, 8);

// sets all info
$account->blocked = false;

// generates password hash if used
switch($config['system.passwords'])
{
    case 'md5':
        $account->password = md5($password);
        break;

    case 'sha1':
        $account->password = sha1($password);
        break;

    case 'plain':
        $account->password = $password;
        break;
}

$account->eMail = $email;
$account->save();

$root = XMLToolbox::createDocumentFragment();
$span = XMLToolbox::createElement('span');
$span->setAttribute('class', 'accountNumber');
$span->addContent($number);
$root->addContents($language['Modules.Account.Created_Number'] . ': ', $span);

// created account number info
$message = $template->createComponent('Message');
$message['message'] = $root;

// check if administrator enabled sending mail
if($config['system.use_mail'])
{
    // tries to send mail with password
    try
    {
        Mail::send($email, $language['Modules.Account.SignupMail_Title'], $language['Modules.Account.SignupMail_Content'] . ': '.$password);
        $message['place'] = $language['Modules.Account.SignupMail_Sent'];
    }
    // if failed then tell user about it
    catch(MailException $error)
    {
        $message['place'] = $language['Modules.Account.SignupMail_Error'];
    }
}
// else jsut display password
else
{
    $root = XMLToolbox::createDocumentFragment();
    $span = XMLToolbox::createElement('span');
    $span->setAttribute('class', 'accountNumber');
    $span->addContent($password);
    $root->addContents($language['Modules.Account.SignupMail_Content'] . ': ', $span);
    $message['place'] = $root;
}

?>
