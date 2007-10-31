<?php

/**#@+
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
 * List of groups.
 * 
 * @package POT
 * @version 0.0.4+SVN
 */
class OTS_Groups_List extends OTS_Base_List
{
/**
 * Deletes group.
 * 
 * @version 0.0.4+SVN
 * @param OTS_Group $group Group to be deleted.
 * @deprecated 0.0.4+SVN Use OTS_Group->delete().
 */
    public function deleteGroup(OTS_Group $group)
    {
        $this->db->query('DELETE FROM ' . $this->db->tableName('groups') . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $group->getId() );
    }

/**
 * Sets list parameters.
 * 
 * This method is called at object creation.
 * 
 * @version 0.0.4+SVN
 * @since 0.0.4+SVN
 */
    public function init()
    {
        $this->table = 'groups';
        $this->class = 'Group';
    }
}

/**#@-*/

?>
