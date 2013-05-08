<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add widget lightbox action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddWidgetLightbox extends BackendBaseActionAdd
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
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addText('thumbnail_width');
		$this->frm->addText('thumbnail_height');
		$this->frm->addDropdown('thumbnail_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')))->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));


		$this->frm->addText('large_width');
		$this->frm->addText('large_height');
		$this->frm->addDropdown('large_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')))->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

		// appearance
		$this->frm->addDropdown('show_close_button', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'false');
		$this->frm->addDropdown('show_arrows', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
		$this->frm->addDropdown('show_caption', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
		$this->frm->addDropdown('caption_type', array('over' => ucfirst(BL::getLabel('Over')),'outside' => ucfirst(BL::getLabel('Outside')),'float' => ucfirst(BL::getLabel('Float')), 'inside' => ucfirst(BL::getLabel('Inside'))), 'outside');
		$this->frm->addText('padding', 25);
		$this->frm->addText('margin', 20);
		$this->frm->addDropdown('modal', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'false');

		// misc
		$this->frm->addDropdown('close_click', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'false');
		$this->frm->addDropdown('media_helper', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');

		// animation
		$this->frm->addDropdown('navigation_effect', array('none' => ucfirst(BL::getLabel('None')), 'elastic' => ucfirst(BL::getLabel('Elastic')) , 'fade' => ucfirst(BL::getLabel('Fade'))), 'none');
		$this->frm->addDropdown('open_effect', array('none' => ucfirst(BL::getLabel('None')), 'elastic' => ucfirst(BL::getLabel('Elastic')) , 'fade' => ucfirst(BL::getLabel('Fade'))), 'none');
		$this->frm->addDropdown('close_effect', array('none' => ucfirst(BL::getLabel('None')), 'elastic' => ucfirst(BL::getLabel('Elastic')) , 'fade' => ucfirst(BL::getLabel('Fade'))), 'none');
		$this->frm->addText('play_speed', 3000);
		$this->frm->addDropdown('loop', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
		
		// thumbnails
		$this->frm->addDropdown('show_thumbnails', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
		$this->frm->addDropdown('thumbnails_position', array('bottom' => ucfirst(BL::getLabel('Bottom')), 'top' => ucfirst(BL::getLabel('top'))), 'bottom');
		$this->frm->addText('thumbnail_navigation_width', 50);
		$this->frm->addText('thumbnail_navigation_height', 50);

		// overlay
		$this->frm->addDropdown('show_overlay', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
		$this->frm->addText('overlay_color', 'rgba(255, 255, 255, 0.85)');


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
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
			self::validateResolution('thumbnail_width');
			self::validateResolution('thumbnail_height');
			self::validateResolution('large_width');
			self::validateResolution('large_height');

			$this->frm->getField('thumbnail_method')->isFilled(BL::getError('FieldIsRequired'));
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
				$title = $this->frm->getField('title')->getValue();

				// build item
				$item['kind'] = 'widget';
				$item['action'] = 'lightbox';
				$item['allow_delete'] = 'Y';
				$item['created_on'] = BackendModel::getUTCDate();
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
				$item['id'] = BackendPhotogalleryModel::insertExtra($item);

				$resolutionThumbnail['extra_id'] = $item['id'];
				$resolutionThumbnail['width'] = $this->frm->getField('thumbnail_width')->getValue();
				$resolutionThumbnail['height'] = $this->frm->getField('thumbnail_height')->getValue();
				$resolutionThumbnail['method'] = $this->frm->getField('thumbnail_method')->getValue();
				$resolutionThumbnail['kind'] = 'thumbnail';

				$resolutionLarge['extra_id'] = $item['id'];
				$resolutionLarge['width'] = $this->frm->getField('large_width')->getValue();
				$resolutionLarge['height'] = $this->frm->getField('large_height')->getValue();
				$resolutionLarge['method'] = $this->frm->getField('large_method')->getValue();
				$resolutionLarge['kind'] = 'large';

				BackendPhotogalleryModel::insertExtraResolution($resolutionThumbnail);

				BackendPhotogalleryModel::insertExtraResolution($resolutionLarge);

				// Create all widgets for each album
				foreach(BackendPhotogalleryModel::getAllAlbums() as $album)
				{
					$resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($item['id']);

					$label = $album['title'] . ' | ' . BackendTemplateModifiers::toLabel($item['action']) . ' | '  . $title  . ' | ' . $resolutionsLabel;
					
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
					
					$id = BackendPhotogalleryModel::insertModulesExtraWidget($extra);

					BackendPhotogalleryModel::insertExtraId(array('album_id' => $album['id'], 'extra_id' => $item['id'], 'modules_extra_id' => $id));
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('extras') . '&report=added-widget&highlight=row-' . $item['id']);
			}
		}
	}
}