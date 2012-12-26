<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This edit widget paged action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryEditWidgetPaged extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendPhotogalleryModel::existsExtra($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the dataGrid
			$this->parse();

			// display the page
			$this->display();
		}
		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('extras') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendPhotogalleryModel::getExtra($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		$this->thumbnail = BackendPhotogalleryModel::getExtraResolutionForKind($this->id, 'thumbnail');
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		parent::parse();
		$this->record['allow_delete'] = $this->record['allow_delete'] == 'Y' ? true : false;
		$this->tpl->assign('item', $this->record);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editWidget');

		// create elements
		$this->frm->addText('thumbnail_width', $this->thumbnail['width']);
		$this->frm->addText('thumbnail_height', $this->thumbnail['height']);
		$this->frm->addDropdown('thumbnail_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->thumbnail['method'])->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));
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
				$item['id'] = $this->id;
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				BackendPhotogalleryModel::updateExtra($item);

				$resolutionThumbnail['width'] = $this->frm->getField('thumbnail_width')->getValue();
				$resolutionThumbnail['height'] = $this->frm->getField('thumbnail_height')->getValue();
				$resolutionThumbnail['method'] = $this->frm->getField('thumbnail_method')->getValue();
				$resolutionThumbnail['kind'] = 'thumbnail';
				$resolutionThumbnail['id'] = $this->thumbnail['id'];

				$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
				$extraHasChanged = false;

				// The thumbnail settings changed!
				if($resolutionThumbnail['width'] != $this->thumbnail['width'] || $resolutionThumbnail['height'] != $this->thumbnail['height'] || $resolutionThumbnail['method'] != $this->thumbnail['method'])
				{
					$extraHasChanged = true;

					// Does the updated one exists in the database
					$exists = BackendPhotogalleryModel::existsResolution($resolutionThumbnail['width'], $resolutionThumbnail['height'], $resolutionThumbnail['kind']);

					// No, generate the new images
					if(!$exists)
					{
						foreach(BackendPhotogalleryModel::getAllImages() as $image)
						{
							$from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
							
							$from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							$to = $setsFilesPath . '/frontend/' . $image['set_id'] . '/' . $resolutionThumbnail['width'] . 'x' . $resolutionThumbnail['height'] . '_' . $resolutionThumbnail['method'] . '/' . $image['filename'];

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

					// Update the resolution
					BackendPhotogalleryModel::updateExtraResolution($resolutionThumbnail);

					// Does the old resolution exists in the database
					$existsOldResolution = BackendPhotogalleryModel::existsResolution($this->thumbnail['width'], $this->thumbnail['height'], $this->thumbnail['method'] );

					// No, generate the new images
					if(!$existsOldResolution)
					{
						// Delete old resolutions
						foreach(BackendPhotogalleryModel::getAllSets() as $set)
						{
							$to = $setsFilesPath . '/frontend/' . $set['id'] . '/' . $this->thumbnail['width'] . 'x' . $this->thumbnail['height'] . '_' . $this->thumbnail['method'];
							SpoonDirectory::delete($to);
						}
					}
				}

				// --------


				// A resolution has changed
				if($extraHasChanged)
				{
					// Get all module_extra_ids for the extra and loop them
					foreach(BackendPhotogalleryModel::getAllModuleExtraIds($this->id) as $extra)
					{
						$resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($extra['extra_id']);

						$album = BackendPhotogalleryModel::getAlbum($extra['album_id']);

						$label = $album['title'] . ' | ' . BackendTemplateModifiers::toLabel($this->record['action']) . ' | ' . $resolutionsLabel;

						$extraItem['label'] = $this->record['action'];
						$extraItem['id'] = $extra['modules_extra_id'];
						$extraItem['data'] = serialize(array('id' => $extra['album_id'],
															'extra_label' => $label,
															'extra_id' => $extra['extra_id'],
															'language' => $album['language'],
															'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $extra['album_id']));

						BackendPhotogalleryModel::updateModulesExtraWidget($extraItem);
					}
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('extras') . '&report=edited-widget&highlight=row-' . $this->record['id']);
			}
		}
	}

}