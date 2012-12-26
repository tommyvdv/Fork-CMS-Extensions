<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add widget paged action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddWidgetPaged extends BackendBaseActionAdd
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
		$this->frm->addText('thumbnail_width');
		$this->frm->addText('thumbnail_height');
		$this->frm->addDropdown('thumbnail_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')))->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));
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
			self::validateResolution('thumbnail_width');
			self::validateResolution('thumbnail_height');
			$this->frm->getField('thumbnail_method')->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['kind'] = 'widget';
				$item['action'] = 'paged';
				$item['allow_delete'] = 'Y';
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['id'] = BackendPhotogalleryModel::insertExtra($item);

				$resolutionThumbnail['extra_id'] = $item['id'];
				$resolutionThumbnail['width'] = $this->frm->getField('thumbnail_width')->getValue();
				$resolutionThumbnail['height'] = $this->frm->getField('thumbnail_height')->getValue();
				$resolutionThumbnail['method'] = $this->frm->getField('thumbnail_method')->getValue();
				$resolutionThumbnail['kind'] = 'thumbnail';

				$exists = BackendPhotogalleryModel::existsResolution($resolutionThumbnail['width'], $resolutionThumbnail['height'], $resolutionThumbnail['kind']);

				if(!$exists)
				{
					foreach(BackendPhotogalleryModel::getAllImages() as $image)
					{
						$from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
						
						SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
						
						$from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
						$to = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/frontend/' . $image['set_id'] . '/' . $resolutionThumbnail['width'] . 'x' . $resolutionThumbnail['height'] . '_' . $resolutionThumbnail['method'] . '/' . $image['filename'];

						// Does the source file exists?
						if(SpoonFile::exists($from))
						{
							$resize = $resolutionThumbnail['method'] == 'resize' ? true : false;
							$thumb = new SpoonThumbnail($from, $resolutionThumbnail['width'] , $resolutionThumbnail['height']);
							$thumb->setAllowEnlargement(true);
							$thumb->setForceOriginalAspectRatio($resize);
							$thumb->parseToFile($to);
						}
					}
				}

				BackendPhotogalleryModel::insertExtraResolution($resolutionThumbnail);

				// Create all widgets for each album
				foreach(BackendPhotogalleryModel::getAllAlbums() as $album)
				{
					$resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($item['id']);

					$label = $album['title'] . ' | ' . BackendTemplateModifiers::toLabel($item['action']) . ' | ' . $resolutionsLabel;
					
					$extra['module'] = $this->getModule();
					$extra['label'] = $item['action'];
					$extra['action'] = $item['action'];
					$extra['data'] = serialize(
										array(
											'id' => $album['id'],
											'extra_label' => $label,
											'extra_id' => $item['id'],
											'language' => $album['language'],
											'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $album['id']
										)
									);
					
					$id =  BackendPhotogalleryModel::insertModulesExtraWidget($extra);

					BackendPhotogalleryModel::insertExtraId(array('album_id' => $album['id'], 'extra_id' => $item['id'], 'modules_extra_id' => $id));
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('extras') . '&report=added-widget&highlight=row-' . $item['id']);
			}
		}
	}
}