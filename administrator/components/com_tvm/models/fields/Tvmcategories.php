<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
class JFormFieldTvmcategories extends JFormFieldList {
 
	protected $type = 'Tvmcategories';
	
	protected function getLabel()
	{
		$options = array();         
		$options[] = array('value' => 1, 'text' => '1. Auswahl'); 
		return $options;
	}
	
	public function getOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id As value, name As text')
			->from('#__tvm_categories')
			->order('name ASC');
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

        // Put "Select an option" on the top of the list.
		array_unshift($options, JHtml::_('select.option', '0', JText::_('Select an option')));

		#return array_merge(parent::getOptions(), $options);
		$options = array(); 
        
		$options[] = array('value' => 1, 'text' => '1. Auswahl'); 
		return $options;
	}
}