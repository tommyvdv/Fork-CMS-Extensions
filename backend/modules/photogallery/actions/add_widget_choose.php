<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add widget choose action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddWidgetChoose extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get all data for the item we want to edit
		$this->getData();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('choose');

		// set hidden values
		$rbtOptionValues[] = array('label' => SpoonFilter::ucfirst(BL::getLabel('Slideshow')), 'value' => 'slideshow');
		$rbtOptionValues[] = array('label' => SpoonFilter::ucfirst(BL::getLabel('Lightbox')), 'value' => 'lightbox');

		/*
		$rbtOptionValues[] = array('label' => SpoonFilter::ucfirst(BL::getLabel('Paged')), 'value' => 'paged');
		$rbtOptionValues[] = array('label' => SpoonFilter::ucfirst(BL::getLabel('Categories')), 'value' => 'categories');
		$rbtOptionValues[] = array('label' => SpoonFilter::ucfirst(BL::getLabel('RelatedByCategories')), 'value' => 'related_by_categories');
		$rbtOptionValues[] = array('label' => SpoonFilter::ucfirst(BL::getLabel('RelatedByTags')), 'value' => 'related_by_tags');
		*/
		
		$this->frm->addRadiobutton('options', $rbtOptionValues, 'slideshow');
	}

	/**
	 * Get the data for a question
	 */
	private function getData()
	{

	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// no errors?
			if($this->frm->isCorrect())
			{
				$option = $this->frm->getField('options')->getValue();

				switch($option)
				{
					case 'slideshow':
						$this->redirect(BackendModel::createURLForAction('add_widget_slideshow'));
						break;
					case 'lightbox':
						$this->redirect(BackendModel::createURLForAction('add_widget_lightbox'));
						break;
					case 'paged':
						$this->redirect(BackendModel::createURLForAction('add_widget_paged'));
						break;
					case 'categories':
						$this->redirect(BackendModel::createURLForAction('add_widget_categories'));
						break;
					case 'related_by_tags':
						$this->redirect(BackendModel::createURLForAction('add_widget_related_by_tags'));
						break;
					case 'related_by_categories':
						$this->redirect(BackendModel::createURLForAction('add_widget_related_by_categories'));
						break;
		}
			}
		}
	}
}