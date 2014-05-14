<?php

namespace Backend\Modules\Photogallery;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryModel;

/**
 * This is the configuration-object for the photogallery module.
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action.
     *
     * @var	string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions.
     *
     * @var	array
     */
    protected $disabledActions = array();
}
