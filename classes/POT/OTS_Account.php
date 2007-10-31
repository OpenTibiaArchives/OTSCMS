<?php

/**#@+
 * @version 0.0.1
 * @since 0.0.1
 */

/**
 * @package POT
 * @version 0.0.4+SVN
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * OTServ account abstraction.
 * 
 * @package POT
 * @version 0.0.4+SVN
 */
class OTS_Account extends OTS_Base_DAO implements IteratorAggregate, Countable
{
/**
 * Account data.
 * 
 * @var array
 */
    private $data = array('email' => '', 'blocked' => false);

/**
 * Creates new account.
 * 
 * Create new account in given range (1 - 9999999 by default).
 * 
 * <p>
 * Remember! This method sets blocked flag to true after account creation!
 * </p>
 * 
 * <p>
 * IMPORTANT: Since 0.0.4 there is group_id field which this method does not support. Account's group_id is set to first one found in database. You should use {@link OTS_Account::createEx() createEx()} method if you want to set group_id field during creation.
 * </p>
 * 
 * @version 0.0.4
 * @param int $min Minimum number.
 * @param int $max Maximum number.
 * @return int Created account number.
 * @example examples/account.php account.php
 * @throws Exception When there are no free account numbers.
 */
    public function create($min = 1, $max = 9999999)
    {
        // loads default group
        $groups = POT::getInstance()->createObject('Groups_List');
        $groups->rewind();
        return $this->createEx( $groups->current(), $min, $max);
    }

/**
 * Creates new account.
 * 
 * Create new account in given range (1 - 9999999 by default) in given group.
 * 
 * <p>
 * Remember! This method sets blocked flag to true after account creation!
 * </p>
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4
 * @param OTS_Group $group Group to be assigned to account.
 * @param int $min Minimum number.
 * @param int $max Maximum number.
 * @return int Created account number.
 * @example examples/create.php account.php
 * @throws Exception When there are no free account numbers.
 */
    public function createEx(OTS_Group $group, $min = 1, $max = 9999999)
    {
        // generates random account number
        $random = rand($min, $max);
        $number = $random;
        $exist = array();

        // reads already existing accounts
        foreach( $this->db->query('SELECT ' . $this->db->fieldName('id') . ' FROM ' . $this->db->tableName('accounts') )->fetchAll() as $account)
        {
            $exist[] = $account['id'];
        }

        // finds unused number
        while(true)
        {
            // unused - found
            if( !in_array($number, $exist) )
            {
                break;
            }

            // used - next one
            $number++;

            // we need to re-set
            if($number > $max)
            {
                $number = $min;
            }

            // we checked all possibilities
            if($number == $random)
            {
                throw new Exception('No free account number are available.');
            }
        }

        // saves blank account info
        $this->data['id'] = $number;
        $this->data['group_id'] = $group->getId();
        $this->data['blocked'] = true;

        $this->db->query('INSERT INTO ' . $this->db->tableName('accounts') . ' (' . $this->db->fieldName('id') . ', ' . $this->db->fieldName('group_id') . ', ' . $this->db->fieldName('password') . ', ' . $this->db->fieldName('email') . ', ' . $this->db->fieldName('blocked') . ') VALUES (' . $number . ', ' . $this->data['group_id'] . ', \'\', \'\', 1)');

        return $number;
    }

/**
 * Loads account with given number.
 * 
 * @version 0.0.4+SVN
 * @param int $id Account number.
 */
    public function load($id)
    {
        // SELECT query on database
        $this->data = $this->db->query('SELECT ' . $this->db->fieldName('id') . ', ' . $this->db->fieldName('group_id') . ', ' . $this->db->fieldName('password') . ', ' . $this->db->fieldName('email') . ', ' . $this->db->fieldName('blocked') . ' FROM ' . $this->db->tableName('accounts') . ' WHERE ' . $this->db->fieldName('id') . ' = ' . (int) $id)->fetch();
    }

/**
 * Loads account by it's e-mail address.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.2
 * @param string $email Account's e-mail address.
 */
    public function find($email)
    {
        // finds player's ID
        $id = $this->db->query('SELECT ' . $this->db->fieldName('id') . ' FROM ' . $this->db->tableName('accounts') . ' WHERE ' . $this->db->fieldName('email') . ' = ' . $this->db->quote($email) )->fetch();

        // if anything was found
        if( isset($id['id']) )
        {
            $this->load($id['id']);
        }
    }

/**
 * Checks if object is loaded.
 * 
 * @return bool Load state.
 */
    public function isLoaded()
    {
        return isset($this->data['id']);
    }

/**
 * Updates account in database.
 * 
 * @version 0.0.4+SVN
 * @throws E_OTS_NotLoaded False if account doesn't have ID assigned.
 */
    public function save()
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        // UPDATE query on database
        $this->db->query('UPDATE ' . $this->db->tableName('accounts') . ' SET ' . $this->db->fieldName('group_id') . ' = ' . $this->data['group_id'] . ', ' . $this->db->fieldName('password') . ' = ' . $this->db->quote($this->data['password']) . ', ' . $this->db->fieldName('email') . ' = ' . $this->db->quote($this->data['email']) . ', ' . $this->db->fieldName('blocked') . ' = ' . (int) $this->data['blocked'] . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $this->data['id']);
    }

/**
 * Account number.
 * 
 * @version 0.0.3
 * @return int Account number.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function getId()
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        return $this->data['id'];
    }

/**
 * Returns group of this account.
 * 
 * @version 0.0.4
 * @since 0.0.4
 * @return OTS_Group Group of which current account is member.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function getGroup()
    {
        if( !isset($this->data['group_id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        $group = POT::getInstance()->createObject('Group');
        $group->load($this->data['group_id']);
        return $group;
    }

/**
 * Assigns account to group.
 * 
 * @param OTS_Group $group Group to be a member.
 */
    public function setGroup(OTS_Group $group)
    {
        $this->data['group_id'] = $group->getId();
    }

/**
 * Account's password.
 * 
 * @version 0.0.3
 * @return string Password.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function getPassword()
    {
        if( !isset($this->data['password']) )
        {
            throw new E_OTS_NotLoaded();
        }

        return $this->data['password'];
    }

/**
 * Sets account's password.
 * 
 * @param string $password Password.
 */
    public function setPassword($password)
    {
        $this->data['password'] = (string) $password;
    }

/**
 * E-mail address.
 * 
 * @version 0.0.3
 * @return string E-mail.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function getEMail()
    {
        if( !isset($this->data['email']) )
        {
            throw new E_OTS_NotLoaded();
        }

        return $this->data['email'];
    }

/**
 * Sets account's email.
 * 
 * @param string $email E-mail address.
 */
    public function setEMail($email)
    {
        $this->data['email'] = (string) $email;
    }

/**
 * Checks if account is blocked.
 * 
 * @version 0.0.3
 * @return bool Blocked state.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function isBlocked()
    {
        if( !isset($this->data['blocked']) )
        {
            throw new E_OTS_NotLoaded();
        }

        return $this->data['blocked'];
    }

/**
 * Unblocks account.
 */
    public function unblock()
    {
        $this->data['blocked'] = false;
    }

/**
 * Blocks account.
 */
    public function block()
    {
        $this->data['blocked'] = true;
    }

/**
 * PACC days.
 * 
 * @version 0.0.4
 * @return int PACC days.
 * @throws E_OTS_NotLoaded If account is not loaded.
 * @deprecated 0.0.3 There is no more premdays field in accounts table.
 */
    public function getPACCDays()
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        return 0;
    }

/**
 * Sets PACC days count.
 * 
 * @version 0.0.4
 * @param int $pacc PACC days.
 * @deprecated 0.0.3 There is no more premdays field in accounts table.
 */
    public function setPACCDays($premdays)
    {
    }

/**
 * Reads custom field.
 * 
 * Reads field by it's name. Can read any field of given record that exists in database.
 * 
 * Note: You should use this method only for fields that are not provided in standard setters/getters (SVN fields). This method runs SQL query each time you call it so it highly overloads used resources.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.3
 * @param string $field Field name.
 * @return string Field value.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function getCustomField($field)
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        $value = $this->db->query('SELECT ' . $this->db->fieldName($field) . ' FROM ' . $this->db->tableName('accounts') . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $this->data['id'])->fetch();
        return $value[$field];
    }

/**
 * Writes custom field.
 * 
 * Write field by it's name. Can write any field of given record that exists in database.
 * 
 * Note: You should use this method only for fields that are not provided in standard setters/getters (SVN fields). This method runs SQL query each time you call it so it highly overloads used resources.
 * 
 * Note: Make sure that you pass $value argument of correct type. This method determinates whether to quote field name. It is safe - it makes you sure that no unproper queries that could lead to SQL injection will be executed, but it can make your code working wrong way. For example: $object->setCustomField('foo', '1'); will quote 1 as as string ('1') instead of passing it as a integer.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.3
 * @param string $field Field name.
 * @param mixed $value Field value.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function setCustomField($field, $value)
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        // quotes value for SQL query
        if(!( is_int($value) || is_float($value) ))
        {
            $value = $this->db->quote($value);
        }

        $this->db->query('UPDATE ' . $this->db->tableName('accounts') . ' SET ' . $this->db->fieldName($field) . ' = ' . $value . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $this->data['id']);
    }

/**
 * List of characters on account.
 * 
 * @version 0.0.4+SVN
 * @return array Array of OTS_Player objects from given account.
 * @throws E_OTS_NotLoaded If account is not loaded.
 * @deprecated 0.0.4+SVN Use getPlayersList().
 */
    public function getPlayers()
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        $players = array();

        foreach( $this->db->query('SELECT ' . $this->db->fieldName('id') . ' FROM ' . $this->db->tableName('players') . ' WHERE ' . $this->db->fieldName('account_id') . ' = ' . $this->data['id'])->fetchAll() as $player)
        {
            // creates new object
            $object = POT::getInstance()->createObject('Player');
            $object->load($player['id']);
            $players[] = $object;
        }

        return $players;
    }

/**
 * List of characters on account.
 * 
 * In difference to {@link OTS_Account::getPlayers() getPlayers() method} this method returns filtered {@link OTS_Players_List OTS_Players_List} object instead of array of {@link OTS_Player OTS_Player} objects. It is more effective since OTS_Player_List doesn't perform all rows loading at once.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 * @return OTS_Players_List List of players from current account.
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function getPlayersList()
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        $ots = POT::getInstance();

        // creates filter
        $filter = $ots->createFilter();
        $filter->compareField('account_id', (int) $this->data['id']);

        // creates list object
        $list = $ots->createObject('Players_List');
        $list->setFilter($filter);

        return $list;
    }

/**
 * Bans current account.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 * @param int $time Time for time until expires (0 - forever).
 */
    public function ban($time = 0)
    {
        // can't ban nothing
        if( !$this->isLoaded() )
        {
            throw new E_OTS_NotLoaded();
        }

        $this->db->query('INSERT INTO ' . $this->db->tableName('bans') . ' (' . $this->db->fieldName('type') . ', ' . $this->db->fieldName('account') . ', ' . $this->db->fieldName('time') . ') VALUES (' . POT::BAN_ACCOUNT . ', ' . $this->data['id'] . ', ' . $time . ')');
    }

/**
 * Deletes ban from current account.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 */
    public function unban()
    {
        // can't unban nothing
        if( !$this->isLoaded() )
        {
            throw new E_OTS_NotLoaded();
        }

        $this->db->query('DELETE FROM ' . $this->db->tableName('bans') . ' WHERE ' . $this->db->fieldName('type') . ' = ' . POT::BAN_ACCOUNT . ' AND ' . $this->db->fieldName('account') . ' = ' . $this->data['id']);
    }

/**
 * Checks if account is banned.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 * @return bool True if account is banned, false otherwise.
 */
    public function isBanned()
    {
        // nothing can't be banned
        if( !$this->isLoaded() )
        {
            throw new E_OTS_NotLoaded();
        }

        $ban = $this->db->query('SELECT COUNT(' . $this->db->fieldName('type') . ') AS ' . $this->db->fieldName('count') . ' FROM ' . $this->db->tableName('bans') . ' WHERE ' . $this->db->fieldName('account') . ' = ' . $this->data['id'] . ' AND (' . $this->db->fieldName('time') . ' > ' . time() . ' OR ' . $this->db->fieldName('time') . ' = 0) AND ' . $this->db->fieldName('type') . ' = ' . POT::BAN_ACCOUNT)->fetch();
        return $ban['count'] > 0;
    }

/**
 * Deletes account.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 * @throws E_OTS_NotLoaded If account is not loaded.
 */
    public function delete()
    {
        if( !isset($this->data['id']) )
        {
            throw new E_OTS_NotLoaded();
        }

        // deletes row from database
        $this->db->query('DELETE FROM ' . $this->db->tableName('accounts') . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $this->data['id']);

        // resets object handle
        unset($this->data['id']);
    }

/**
 * Returns players iterator.
 * 
 * There is no need to implement entire Iterator interface since we have {@link OTS_Players_List players list class} for it.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 * @throws E_OTS_NotLoaded If account is not loaded.
 * @return Iterator List of players.
 */
    public function getIterator()
    {
        return $this->getPlayersList();
    }

/**
 * Returns number of player within.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 * @throws E_OTS_NotLoaded If account is not loaded.
 * @return int Count of players.
 */
    public function count()
    {
        // count( $this->getPlayersList() ); will be slower
        return $this->getPlayersList()->count();
    }
}

/**#@-*/

?>
