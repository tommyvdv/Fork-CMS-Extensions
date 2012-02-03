<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general blog settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendPhotogallerySettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('settings');

		// add fields for pagination
		$this->frm->addDropdown('overview_albums_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_albums_number_of_items', 10));
		$this->frm->addDropdown('overview_categories_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_categories_number_of_items', 10));
		$this->frm->addDropdown('related_list_categories_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_list_categories_number_of_items', 10));
		$this->frm->addDropdown('related_list_tags_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_list_tags_number_of_items', 10));
		$this->frm->addDropdown('related_categories_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_categories_number_of_items', 10));
		$this->frm->addDropdown('related_tags_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_tags_number_of_items', 10));

		// add fields for SEO
		$this->frm->addCheckbox('ping_services', BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false));

		// add fields for RSS
		$this->frm->addCheckbox('rss_meta', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), true));
		$this->frm->addText('rss_title', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage()));
		$this->frm->addTextarea('rss_description', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage()));
		$this->frm->addText('feedburner_url', BackendModel::getModuleSetting($this->URL->getModule(), 'feedburner_url_' . BL::getWorkingLanguage()));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			if($this->frm->isCorrect())
			{
				// shorten fields
				$feedburnerURL = $this->frm->getField('feedburner_url');

				// validation
				$this->frm->getField('rss_title')->isFilled(BL::err('FieldIsRequired'));

				// feedburner URL is set
				if($feedburnerURL->isFilled())
				{
					// check if http:// is set and add if necessary
					$feedburner = !strstr($feedburnerURL->getValue(), 'http://') ? 'http://' . $feedburnerURL->getValue() : $feedburnerURL->getValue();
	
					// check if feedburner URL is valid
					if(!SpoonFilter::isURL($feedburner)) $feedburnerURL->addError(BL::err('InvalidURL'));
				}

				// init variable
				else $feedburner = null;

				// set our settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'overview_albums_number_of_items', (int) $this->frm->getField('overview_albums_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'overview_categories_number_of_items', (int) $this->frm->getField('overview_categories_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'related_list_categories_number_of_items', (int) $this->frm->getField('related_list_categories_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'related_list_tags_number_of_items', (int) $this->frm->getField('related_list_tags_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'related_categories_number_of_items', (int) $this->frm->getField('related_categories_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'related_tags_number_of_items', (int) $this->frm->getField('related_tags_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'ping_services', (bool) $this->frm->getField('ping_services')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), $this->frm->getField('rss_meta')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'feedburner_url_' . BL::getWorkingLanguage(), $feedburner);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
