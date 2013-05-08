<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This edit widget lightbox action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryEditWidgetLightbox extends BackendBaseActionEdit
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
		$this->large = BackendPhotogalleryModel::getExtraResolutionForKind($this->id, 'large');
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

		$this->frm->addText('title', $this->record['data']['settings']['title'], null, 'inputText title', 'inputTextError title');

		// create elements
		$this->frm->addText('thumbnail_width', $this->thumbnail['width']);
		$this->frm->addText('thumbnail_height', $this->thumbnail['height']);
		$this->frm->addDropdown('thumbnail_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->thumbnail['method'])->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

		$this->frm->addText('large_width', $this->large['width']);
		$this->frm->addText('large_height', $this->large['height']);
		$this->frm->addDropdown('large_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->large['method'])->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

		// appearance
		$this->frm->addDropdown('show_close_button', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_close_button']);
		$this->frm->addDropdown('show_arrows', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_arrows']);
		$this->frm->addDropdown('show_caption', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_caption']);
		$this->frm->addDropdown('caption_type', array('over' => ucfirst(BL::getLabel('Over')),'outside' => ucfirst(BL::getLabel('Outside')),'float' => ucfirst(BL::getLabel('Float')), 'inside' => ucfirst(BL::getLabel('Inside'))), $this->record['data']['settings']['caption_type']);
		$this->frm->addText('padding', $this->record['data']['settings']['padding']);
		$this->frm->addText('margin', $this->record['data']['settings']['margin']);
		$this->frm->addDropdown('modal', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['modal']);

		// misc
		$this->frm->addDropdown('close_click', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['close_click']);
		$this->frm->addDropdown('media_helper', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['media_helper']);

		// animation
		$this->frm->addDropdown('navigation_effect', array('none' => ucfirst(BL::getLabel('None')), 'elastic' => ucfirst(BL::getLabel('Elastic')) , 'fade' => ucfirst(BL::getLabel('Fade'))), $this->record['data']['settings']['navigation_effect']);
		$this->frm->addDropdown('open_effect', array('none' => ucfirst(BL::getLabel('None')), 'elastic' => ucfirst(BL::getLabel('Elastic')) , 'fade' => ucfirst(BL::getLabel('Fade'))), $this->record['data']['settings']['open_effect']);
		$this->frm->addDropdown('close_effect', array('none' => ucfirst(BL::getLabel('None')), 'elastic' => ucfirst(BL::getLabel('Elastic')) , 'fade' => ucfirst(BL::getLabel('Fade'))), $this->record['data']['settings']['close_effect']);
		$this->frm->addText('play_speed', $this->record['data']['settings']['play_speed']);
		$this->frm->addDropdown('loop', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['loop']);
		
		// thumbnails
		$this->frm->addDropdown('show_thumbnails', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_thumbnails']);
		$this->frm->addDropdown('thumbnails_position', array('bottom' => ucfirst(BL::getLabel('Bottom')), 'top' => ucfirst(BL::getLabel('top'))), $this->record['data']['settings']['thumbnails_position']);
		$this->frm->addText('thumbnail_navigation_width', $this->record['data']['settings']['thumbnail_navigation_width']);
		$this->frm->addText('thumbnail_navigation_height', $this->record['data']['settings']['thumbnail_navigation_height']);

		// overlay
		$this->frm->addDropdown('show_overlay', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_overlay']);
		$this->frm->addText('overlay_color', $this->record['data']['settings']['overlay_color']);
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
			self::validateResolution('large_width');
			self::validateResolution('large_height');

			$this->frm->getField('thumbnail_method')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('large_method')->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				$title = $this->frm->getField('title')->getValue();

				// build item
				$item['id'] = $this->id;
				$item['edited_on'] = BackendModel::getUTCDate();

				$item['data'] = serialize(
									array(
										'settings' => array(
												'title' => $title,
												'show_close_button' => $this->frm->getField('show_close_button')->getValue(),
												'show_arrows' => $this->frm->getField('show_arrows')->getValue(),
												'show_caption' => $this->frm->getField('show_caption')->getValue(),
												'caption_type' => $this->frm->getField('caption_type')->getValue(),
												'padding' => $this->frm->getField('padding')->getValue(),
												'margin' => $this->frm->getField('margin')->getValue(),
												'modal' => $this->frm->getField('modal')->getValue(),
												'close_click' => $this->frm->getField('close_click')->getValue(),
												'media_helper' => $this->frm->getField('media_helper')->getValue(),
												'navigation_effect' => $this->frm->getField('navigation_effect')->getValue(),
												'open_effect' => $this->frm->getField('open_effect')->getValue(),
												'close_effect' => $this->frm->getField('close_effect')->getValue(),
												'play_speed' => $this->frm->getField('play_speed')->getValue(),
												'loop' => $this->frm->getField('loop')->getValue(),
												'show_thumbnails' => $this->frm->getField('show_thumbnails')->getValue(),
												'thumbnails_position' => $this->frm->getField('thumbnails_position')->getValue(),
												'thumbnail_navigation_width' => $this->frm->getField('thumbnail_navigation_width')->getValue(),
												'thumbnail_navigation_height' => $this->frm->getField('thumbnail_navigation_height')->getValue(),
												'show_overlay' => $this->frm->getField('show_overlay')->getValue(),
												'overlay_color' => $this->frm->getField('overlay_color')->getValue(),
										)
									)
								);

				// insert the item
				BackendPhotogalleryModel::updateExtra($item);

				$resolutionThumbnail['width'] = $this->frm->getField('thumbnail_width')->getValue();
				$resolutionThumbnail['height'] = $this->frm->getField('thumbnail_height')->getValue();
				$resolutionThumbnail['method'] = $this->frm->getField('thumbnail_method')->getValue();
				$resolutionThumbnail['kind'] = 'thumbnail';
				$resolutionThumbnail['id'] = $this->thumbnail['id'];

				$resolutionLarge['width'] = $this->frm->getField('large_width')->getValue();
				$resolutionLarge['height'] = $this->frm->getField('large_height')->getValue();
				$resolutionLarge['method'] = $this->frm->getField('large_method')->getValue();
				$resolutionLarge['kind'] = 'large';
				$resolutionLarge['id'] = $this->large['id'];

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
							
							SpoonDirectory::create(FRONTEND_FILES_PATH . '/original/' . $this->URL->getModule() . '/sets/' . $image['set_id']);
							
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

				// The thumbnail settings changed!
				if($resolutionLarge['width'] != $this->large['width'] || $resolutionLarge['height'] != $this->large['height'] || $resolutionLarge['method'] != $this->large['method'])
				{
					$extraHasChanged = true;

					// Does the updated one exists in the database
					$exists = BackendPhotogalleryModel::existsResolution($resolutionLarge['width'], $resolutionLarge['height'], $resolutionLarge['kind']);

					// No, generate the new images
					if(!$exists)
					{
						foreach(BackendPhotogalleryModel::getAllImages() as $image)
						{
							$from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
							
							$from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							$to = $setsFilesPath . '/frontend/' . $image['set_id'] . '/' . $resolutionLarge['width'] . 'x' . $resolutionLarge['height'] . '_' . $resolutionLarge['method'] . '/' . $image['filename'];

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

					// Update the resolution
					BackendPhotogalleryModel::updateExtraResolution($resolutionLarge);

					// Does the old resolution exists in the database
					$existsOldResolution = BackendPhotogalleryModel::existsResolution($this->large['width'], $this->large['height'], $this->large['method'] );

					// No, generate the new images
					if(!$existsOldResolution)
					{
						// Delete old resolutions
						foreach(BackendPhotogalleryModel::getAllSets() as $set)
						{
							$to = $setsFilesPath . '/frontend/' . $set['id'] . '/' . $this->large['width'] . 'x' . $this->large['height'] . '_' . $this->large['method'];
							SpoonDirectory::delete($to);
						}
					}
				}


			
				// Get all module_extra_ids for the extra and loop them
				foreach(BackendPhotogalleryModel::getAllModuleExtraIds($this->id) as $extra)
				{
					$resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($extra['extra_id']);

					$album = BackendPhotogalleryModel::getAlbum($extra['album_id']);

					$label = $album['title'] . ' | ' . BackendTemplateModifiers::toLabel($this->record['action']) . ' | '  . $title . ' | '  . $resolutionsLabel;

					$extraItem['label'] = $this->record['action'];
					$extraItem['id'] = $extra['modules_extra_id'];
					$extraItem['data'] = serialize(array('id' => $extra['album_id'],
														'extra_label' => $label,
														'extra_id' => $extra['extra_id'],
														'language' => $album['language'],
														'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $extra['album_id']));
					
					BackendPhotogalleryModel::updateModulesExtraWidget($extraItem);
				}
				

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit_widget_lightbox') . '&report=edited-widget&id=' . $this->record['id']);
			}
		}
	}

}