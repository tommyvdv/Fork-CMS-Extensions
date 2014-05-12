<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* Re-order the album images
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */

class BackendPhotogalleryAjaxImagesSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));
		$count = count($ids);

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// build item
			$item['id'] = (int) $id;

			// change sequence
			$item['sequence'] = $count--;
			
			// update sequence
			if(BackendPhotogalleryModel::existsImage($item['id'])) BackendPhotogalleryModel::updateImage($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
