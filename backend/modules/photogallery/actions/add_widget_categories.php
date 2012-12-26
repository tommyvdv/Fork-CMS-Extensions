<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add widget categories action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddWidgetCategories extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the dataGrid
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
		$this->frm = new BackendForm('addWidget');

		// create elements
		$this->frm->addText('large_width');
		$this->frm->addText('large_height');
		$this->frm->addDropdown('large_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')))->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));
	}

	/**
	 * Validate the resolution
	 *
	 * @param string $field The field to validate
	 */
	private function validateResolution($field)
	{
		if($this->frm->getField($field)->isFilled(BL::getError('FieldIsRequired')))
		{
			if($this->frm->getField($field)->isFloat(BL::getError('InvalidNumber')))
			{
				$this->frm->getField($field)->isGreaterThan(0, SpoonFilter::ucfirst(BL::getError('FieldMustBeGreatherThenZero')));
			}
		}
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

			// validate fields
			self::validateResolution('large_width');
			self::validateResolution('large_height');
			$this->frm->getField('large_method')->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['kind'] = 'widget';
				$item['action'] = 'categories';
				$item['allow_delete'] = 'Y';
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['id'] = BackendPhotogalleryModel::insertExtra($item);

				$resolutionLarge['extra_id'] = $item['id'];
				$resolutionLarge['width'] = $this->frm->getField('large_width')->getValue();
				$resolutionLarge['height'] = $this->frm->getField('large_height')->getValue();
				$resolutionLarge['method'] = $this->frm->getField('large_method')->getValue();
				$resolutionLarge['kind'] = 'large';

				$exists = BackendPhotogalleryModel::existsResolution($resolutionLarge['width'], $resolutionLarge['height'], $resolutionLarge['kind']);

				if(!$exists)
				{
					foreach(BackendPhotogalleryModel::getAllImages() as $image)
					{
						
						$from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
						
						SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
						
						$from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
						$to = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/frontend/' . $image['set_id'] . '/' . $resolutionLarge['width'] . 'x' . $resolutionLarge['height'] . '_' . $resolutionLarge['method'] . '/' . $image['filename'];
						
						// Does the source file exists?
						if(SpoonFile::exists($from))
						{
							$resize = $resolutionLarge['method'] == 'resize' ? true : false;
							$thumb = new SpoonThumbnail($from, $resolutionLarge['width'] , $resolutionLarge['height']);
							$thumb->setAllowEnlargement(true);
							$thumb->setForceOriginalAspectRatio($resize);
							$thumb->parseToFile($to);
						}
					}
				}

				BackendPhotogalleryModel::insertExtraResolution($resolutionLarge);
	
				$resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($item['id']);

				$label = BackendTemplateModifiers::toLabel($item['action']) . ' | ' . $resolutionsLabel;

				$extra['module'] = $this->getModule();
				$extra['label'] = $item['action'];
				$extra['action'] = $item['action'];
				$extra['data'] = serialize(
									array(
										'extra_label' => $label,
										'extra_id' => $item['id'],
										'language' => BL::getWorkingLanguage(),
									)
								);
								
				$id = BackendPhotogalleryModel::insertModulesExtraWidget($extra);

				BackendPhotogalleryModel::insertExtraId(array('extra_id' => $item['id'], 'modules_extra_id' => $id));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('extras') . '&report=added-widget&highlight=row-' . $item['id']);
			}
		}
	}
}