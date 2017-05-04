<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.html.toolbar.button' );

?>

<h1><?php echo $this->msg; ?></h1>

<?php if ($this->isTrainer) : ?>
<span>
<?php 
	echo '<a href="?option=com_tvm&task=insert">'.JText::_('COM_TVM_SITE_ADD_ENTRY').'</a>';
?>
</span>

<?php endif; // isTrainer?> 
