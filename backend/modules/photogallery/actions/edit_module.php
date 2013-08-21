<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This edit module action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryEditModule extends BackendBaseActionEdit
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

		$this->album_detail_overview_thumbnail = BackendPhotogalleryModel::getExtraResolutionForKind($this->id, 'album_detail_overview_thumbnail');
		$this->album_overview_thumbnail = BackendPhotogalleryModel::getExtraResolutionForKind($this->id, 'album_overview_thumbnail');

		$this->large = BackendPhotogalleryModel::getExtraResolutionForKind($this->id, 'large');
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		parent::parse();
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
		$this->frm->addText('album_detail_overview_thumbnail_width', $this->album_detail_overview_thumbnail['width']);
		$this->frm->addText('album_detail_overview_thumbnail_height', $this->album_detail_overview_thumbnail['height']);
		$this->frm->addDropdown('album_detail_overview_thumbnail_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->album_detail_overview_thumbnail['method'])->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

		$this->frm->addText('album_overview_thumbnail_width', $this->album_overview_thumbnail['width']);
		$this->frm->addText('album_overview_thumbnail_height', $this->album_overview_thumbnail['height']);
		$this->frm->addDropdown('album_overview_thumbnail_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->album_overview_thumbnail['method'])->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

		$this->frm->addText('large_width', $this->large['width']);
		$this->frm->addText('large_height', $this->large['height']);
		$this->frm->addDropdown('large_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->large['method'])->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

		$actionValues = array(
			array('value' => 'paged', 'label' => SpoonFilter::ucfirst(BL::lbl('Paged'))),
			array('value' => 'lightbox', 'label' => SpoonFilter::ucfirst(BL::lbl('Lightbox'))),
		);

		$this->frm->addRadiobutton('action', $actionValues, $this->record['data']['action']);
		
		$actionValues = array(
			array('value' => 'albums', 'label' => SpoonFilter::ucfirst(BL::lbl('Albums'))),
			array('value' => 'categories', 'label' => SpoonFilter::ucfirst(BL::lbl('Categories'))),
		);

		$this->frm->addRadiobutton('display', $actionValues, $this->record['data']['display']);

		// appearance
		$this->frm->addDropdown('show_close_button', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_close_button']);
		$this->frm->addDropdown('show_arrows', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_arrows']);
		$this->frm->addDropdown('show_caption', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_caption']);
		$this->frm->addDropdown('caption_type', array('over' => ucfirst(BL::getLabel('Over')),'outside' => ucfirst(BL::getLabel('Outside')),'float' => ucfirst(BL::getLabel('Float')), 'inside' => ucfirst(BL::getLabel('Inside'))), $this->record['data']['settings']['caption_type']);
		$this->frm->addText('padding', $this->record['data']['settings']['padding']);
		$this->frm->addText('margin', $this->record['data']['settings']['margin']);
		$this->frm->addDropdown('modal', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['modal']);
		$this->frm->addDropdown('show_hover_icon', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_hover_icon']);

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
			self::validateResolution('album_detail_overview_thumbnail_width');
			self::validateResolution('album_detail_overview_thumbnail_height');

			self::validateResolution('album_overview_thumbnail_width');
			self::validateResolution('album_overview_thumbnail_height');

			self::validateResolution('large_width');
			self::validateResolution('large_height');

			$this->frm->getField('album_detail_overview_thumbnail_method')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('album_overview_thumbnail_method')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('large_method')->isFilled(BL::getError('FieldIsRequired'));

			$this->frm->getField('padding')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('margin')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('play_speed')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('thumbnail_navigation_width')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('thumbnail_navigation_height')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('overlay_color')->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				$action = $this->frm->getField('action')->getValue();
				$display = $this->frm->getField('display')->getValue();

				$lightboxSettings =  array(
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
										'show_hover_icon' => $this->frm->getField('show_hover_icon')->getValue(),
									);

				$data = array('action' => $action, 'display' => $display, 'settings' => $lightboxSettings);

				// build item
				$item['id'] = $this->id;
				$item['edited_on'] = BackendModel::getUTCDate();
				$item['data'] = serialize($data);

				// insert the item
				BackendPhotogalleryModel::updateExtra($item);

				$resolutionDetailThumbnail['width'] = $this->frm->getField('album_detail_overview_thumbnail_width')->getValue();
				$resolutionDetailThumbnail['height'] = $this->frm->getField('album_detail_overview_thumbnail_height')->getValue();
				$resolutionDetailThumbnail['method'] = $this->frm->getField('album_detail_overview_thumbnail_method')->getValue();
				$resolutionDetailThumbnail['kind'] = 'album_detail_overview_thumbnail';
				$resolutionDetailThumbnail['id'] = $this->album_detail_overview_thumbnail['id'];

				$resolutionOverviewThumbnail['width'] = $this->frm->getField('album_overview_thumbnail_width')->getValue();
				$resolutionOverviewThumbnail['height'] = $this->frm->getField('album_overview_thumbnail_height')->getValue();
				$resolutionOverviewThumbnail['method'] = $this->frm->getField('album_overview_thumbnail_method')->getValue();
				$resolutionOverviewThumbnail['kind'] = 'album_overview_thumbnail';
				$resolutionOverviewThumbnail['id'] = $this->album_overview_thumbnail['id'];

				$resolutionLarge['width'] = $this->frm->getField('large_width')->getValue();
				$resolutionLarge['height'] = $this->frm->getField('large_height')->getValue();
				$resolutionLarge['method'] = $this->frm->getField('large_method')->getValue();
				$resolutionLarge['kind'] = 'large';
				$resolutionLarge['id'] = $this->large['id'];

				$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
				$extraHasChanged = false;

				if($action != $this->record['data']['action']) $extraHasChanged = true;
				if($display != $this->record['data']['display']) $extraHasChanged = true;

				// The album_detail_overview_thumbnail settings changed!
				if($resolutionDetailThumbnail['width'] != $this->album_detail_overview_thumbnail['width'] || $resolutionDetailThumbnail['height'] != $this->album_detail_overview_thumbnail['height'] || $resolutionDetailThumbnail['method'] != $this->album_detail_overview_thumbnail['method'])
				{
					$extraHasChanged = true;
					
					// Update the resolution
					BackendPhotogalleryModel::updateExtraResolution($resolutionDetailThumbnail);
					
					// Does the updated one exists in the database
					$exists = BackendPhotogalleryModel::existsResolution($resolutionDetailThumbnail['width'], $resolutionDetailThumbnail['height'], $resolutionDetailThumbnail['kind']);

					// No, generate the new images
					if(!$exists)
					{
						foreach(BackendPhotogalleryModel::getAllImages() as $image)
						{
							$from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
							
							$from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
								
							$to = $setsFilesPath . '/frontend/' . $image['set_id'] . '/' . $resolutionDetailThumbnail['width'] . 'x' . $resolutionDetailThumbnail['height'] . '_' . $resolutionDetailThumbnail['method'] . '/' . $image['filename'];

							// Does the source file exists?
							if(SpoonFile::exists($from))
							{
								$resize = $resolutionDetailThumbnail['method'] == 'resize' ? true : false;
								$thumb = new SpoonThumbnail($from, $resolutionDetailThumbnail['width'] , $resolutionDetailThumbnail['height']);
								$thumb->setAllowEnlargement(true);
								$thumb->setForceOriginalAspectRatio($resize);
								$thumb->parseToFile($to);
							}
						}
					}

					// Does the old resolution exists in the database
					$existsOldResolution = BackendPhotogalleryModel::existsResolution($this->album_detail_overview_thumbnail['width'], $this->album_detail_overview_thumbnail['height'], $this->album_detail_overview_thumbnail['method'] );

					// No, generate the new images
					if(!$existsOldResolution)
					{
						// Delete old resolutions
						foreach(BackendPhotogalleryModel::getAllSets() as $set)
						{
							$to = $setsFilesPath . '/frontend/' . $set['id'] . '/' . $this->album_detail_overview_thumbnail['width'] . 'x' . $this->album_detail_overview_thumbnail['height'] . '_' . $this->album_detail_overview_thumbnail['method'];
							SpoonDirectory::delete($to);
						}
					}
				}

				// The album_overview_thumbnail settings changed!
				if($resolutionOverviewThumbnail['width'] != $this->album_overview_thumbnail['width'] || $resolutionOverviewThumbnail['height'] != $this->album_overview_thumbnail['height'] || $resolutionOverviewThumbnail['method'] != $this->album_overview_thumbnail['method'])
				{
					$extraHasChanged = true;
					
					// Update the resolution
					BackendPhotogalleryModel::updateExtraResolution($resolutionOverviewThumbnail);
					
					// Does the updated one exists in the database
					$exists = BackendPhotogalleryModel::existsResolution($resolutionOverviewThumbnail['width'], $resolutionOverviewThumbnail['height'], $resolutionOverviewThumbnail['kind']);

					// No, generate the new images
					if(!$exists)
					{
						foreach(BackendPhotogalleryModel::getAllImages() as $image)
						{
							$from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
							
							$from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
							
							$to = $setsFilesPath . '/frontend/' . $image['set_id'] . '/' . $resolutionOverviewThumbnail['width'] . 'x' . $resolutionOverviewThumbnail['height'] . '_' . $resolutionOverviewThumbnail['method'] . '/' . $image['filename'];

							// Does the source file exists?
							if(SpoonFile::exists($from))
							{
								$resize = $resolutionOverviewThumbnail['method'] == 'resize' ? true : false;
								$thumb = new SpoonThumbnail($from, $resolutionOverviewThumbnail['width'] , $resolutionOverviewThumbnail['height']);
								$thumb->setAllowEnlargement(true);
								$thumb->setForceOriginalAspectRatio($resize);
								$thumb->parseToFile($to);
							}
						}
					}


					// Does the old resolution exists in the database
					$existsOldResolution = BackendPhotogalleryModel::existsResolution($this->album_overview_thumbnail['width'], $this->album_overview_thumbnail['height'], $this->album_overview_thumbnail['method'] );

					// No, generate the new images
					if(!$existsOldResolution)
					{
						// Delete old resolutions
						foreach(BackendPhotogalleryModel::getAllSets() as $set)
						{
							$to = $setsFilesPath . '/frontend/' . $set['id'] . '/' . $this->album_overview_thumbnail['width'] . 'x' . $this->album_overview_thumbnail['height'] . '_' . $this->album_overview_thumbnail['method'];
							SpoonDirectory::delete($to);
						}
					}
				}


				// --------

				// The album_detail_overview_thumbnail settings changed!
				if($resolutionLarge['width'] != $this->large['width'] || $resolutionLarge['height'] != $this->large['height'] || $resolutionLarge['method'] != $this->large['method'])
				{
					$extraHasChanged = true;
					
					
					// Update the resolution
					BackendPhotogalleryModel::updateExtraResolution($resolutionLarge);
					
					
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

				// A resolution has changed
				if($extraHasChanged)
				{
					$extraItem['data'] = serialize(array('extra_id' => $this->id, 'action' => $data['action'], 'display' => $data['display']));
					$extraItem['module'] = $this->URL->getModule();
					$extraItem['type'] = 'block';

					BackendPhotogalleryModel::updateModulesExtraBlockByModule($extraItem);
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit_module') . '&report=edited-module&id=' . $this->record['id']);
			}
		}
	}

}