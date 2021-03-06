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

// protection from hackers
$name = InputData::read('name');

// loads monster file
$monster = $ots->getMonstersList()->getMonster($name);

// there has to be an image for that spell - that is the way how you can select which spells should be displayed
if(!($extension = Toolbox::imageExists('Monsters/' . $name) ))
{
    throw new HandledException('NotToDisplay');
}

$voices = $monster->voices;

// composes quotes
foreach($voices as $index => $voice)
{
    $voices[$index] = '<span style="font-style: italic;">&quot;' . $voice . '&quot;</span>';
}

$loot = $monster->items;
$names = array();

if( !empty($loot) )
{
    // replaces ids by names
    foreach($loot as $item)
    {
        // checks if there is name for such item
        if( !isset($names[$item->id]) )
        {
            $names[$item->id] = $item->name;
        }
    }
}

$defenses = array_merge($monster->defenses, $monster->immunities);

foreach($defenses as $index => $defense)
{
    $defemses[$index] = ucfirst($defense);
}

$attacks = $monster->attacks;

foreach($attacks as $index => $attack)
{
    $attacks[$index] = ucfirst($attack);
}

// puts informations into monsters data
$data = $template->createComponent('LibraryPage');
$data['header'] = $language['Modules.Library.MonsterInformation'];
$data['name'] = $monster->name;
$data['experience'] = $monster->experience;
$data['health'] = $monster->health;
$data['voices'] = empty($voices) ? '' : XMLToolbox::inparse( implode(', ', $voices) );
$data['defenses'] = implode(', ', $defenses);
$data['attacks'] = empty($attacks) ? '' : XMLToolbox::inparse( implode(', ', $attacks) );
$data['loot'] = implode(', ', $names);
$data['image'] = str_replace('\\', '/', $config['directories.images']) . 'Monsters/' . $name . $extension;

// sets labels
$data->addLabel('experience', $language['Modules.Library.MonsterExperience']);
$data->addLabel('health', $language['Modules.Library.MonsterHealth']);
$data->addLabel('attacks', $language['Modules.Library.MonsterAttacks']);
$data->addLabel('voices', $language['Modules.Library.MonsterVoices']);
$data->addLabel('defenses', $language['Modules.Library.MonsterDefenses']);
$data->addLabel('loot', $language['Modules.Library.MonsterLoot']);

?>
