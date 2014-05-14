<?php

namespace Backend\Modules\Photogallery\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;

/**
 * Images sequence ajax action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */

class ImagesSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

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
