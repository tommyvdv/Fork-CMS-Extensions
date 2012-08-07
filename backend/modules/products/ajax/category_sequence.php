<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* Re-order the albums
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */

class BackendPhotogalleryAjaxCategorySequence extends BackendBaseAJAXAction
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

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// build item
			$item['id'] = (int) $id;

			// fetch entire record
			$item = BackendPhotogalleryModel::getCategory($id);

			// change sequence
			$item['sequence'] = $i + 1;

			unset($item['url']);
			unset($item['publish_on']);

			// update sequence
			if(BackendPhotogalleryModel::existsCategory($item['id'])) BackendPhotogalleryModel::updateCategory($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
