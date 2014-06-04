<?php

namespace Frontend\Modules\Photogallery\Engine;

use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendURL;
use Frontend\Core\Engine\Theme as FrontendTheme;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

/**
 * In this file we store all generic functions that we will be using in the photogallery module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Helper
{
    public static function mapModifiers($tpl)
    {
        // parse image methods
        $tpl->mapModifier('createimageresolution', array('Frontend\Modules\Photogallery\Engine\Helper', 'createImageResolution'));
    }

    public static function createImageResolution($var, $set_id, $filename, $kind = 'backend_thumb', $dir = 'photogallery', $watermark = false)
    {
        $resolution = self::getResolution($kind);
        if(!$resolution) return false;
        $width = $resolution['width']; $height = $resolution['height']; $method = $resolution['method']; $watermark = $resolution['watermark'];

        $watermark = \SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $watermark) ? FRONTEND_FILES_PATH . '/' . $watermark : null;
        $allow_watermark = $resolution['allow_watermark'] == 'Y';
        $watermark_position = self::translatePosition($resolution['watermark_position']);
        $watermark_padding = (int) $resolution['watermark_padding'];

        $original   = self::getOriginalPath($set_id, $filename, $dir);
        $image      = self::getImagePath($set_id, $filename, array('width' => $width, 'height' => $height, 'method' => $method), $dir);
        
        //Spoon::dump(SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original));
//Spoon::dump($original);
        //if($resolution['regenerate'] == 'Y')
            //Spoon::dump($dir);

        if(
            (!\SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $image) && \SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original)) ||
            $resolution['regenerate'] == 'Y'
        )
        {
            if($resolution['regenerate'] == 'Y') self::updateResolution(array('regenerate' => 'N', 'id' => $resolution['id']));

            $forceOriginalAspectRatio = $method == 'crop' ? false : true;
            $allowEnlargement = true;
            
            $thumb = new \SpoonThumbnail(FRONTEND_FILES_PATH . '/' . $original, $width, $height);
            //if(!$height) Spoon::dump($height);
            $thumb->setAllowEnlargement($allowEnlargement);
            $thumb->setForceOriginalAspectRatio($width && $height ? $forceOriginalAspectRatio : true);
            if($watermark && $allow_watermark && FrontendModel::getModuleSetting('Photogallery', 'allow_watermark'))
            {
                if($watermark_position) $thumb->setWatermarkPosition($watermark_position[0], $watermark_position[1]);
                if($watermark_padding) $thumb->setWatermarkPadding($watermark_padding);
                $thumb->setWatermark($watermark);
            }
            $thumb->parseToFile(FRONTEND_FILES_PATH . '/' . $image, 100);
        }

        return FRONTEND_FILES_URL . '/' . $image;
    }

    public static function getResolution($kind)
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT *,
                IF(width_null = "Y", NULL, width) AS width,
                IF(height_null = "Y", NULL, height) AS height,
                IF(watermark IS NULL, NULL, CONCAT("/products/watermarks/source/", watermark)) AS watermark
            FROM photogallery_resolutions
            WHERE kind = ?',
            $kind,
            'id'
        );
    }

    public static function translatePosition($positionInt = 0)
    {
        switch((int) $positionInt)
        {
            case 1: return array('left', 'top');
            case 2: return array('center', 'top');
            case 3: return array('right', 'top');
            case 4: return array('left', 'center');
            case 5: return array('center', 'center');
            case 6: return array('right', 'center');
            case 7: return array('left', 'bottom');
            case 8: return array('center', 'bottom');
            case 9: return array('right', 'bottom');
            default: return 0;
        }
    }

    /**
     * Generate a correct path
     *
     * @return string
     */
    public static function getPathJS($file, $module)
    {
        $file = (string) $file;
        $module = (string) $module;

        $theme = FrontendTheme::getTheme();
        $themePath = '/src/Frontend/Themes/' . $theme . '/Core/Js';

        $filePath = $themePath . $file;

        if(\SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $filePath))) return $filePath;

        return '/src/Frontend/Modules/' . $module . '/Js' . $file;
    }

    /**
     * Generate a correct path
     *
     * @return string
     */
    public static function getPathCSS($file, $module)
    {
        $file = (string) $file;
        $module = (string) $module;

        $theme = FrontendTheme::getTheme();
        $themePath = '/frontend/themes/' . $theme . '/core/layout/css';

        $filePath = $themePath . $file;

        if(\SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $filePath))) return $filePath;

        return '/frontend/modules/' . $module . '/layout/css' . $file;
    }

    public static function getImagePath($set_id, $filename, $resolution)
    {
        return 'photogallery/sets/frontend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . $resolution['method'] . '/'  . $filename;
    }

    public static function getOriginalPath($set_id, $filename)
    {
        return 'photogallery/sets/original/' . $set_id . '/'  . $filename;
    }
    
    public static function createImage($var, $set_id, $filename, $width, $height, $method = 'crop')
    {
        $original   = self::getOriginalPath($set_id, $filename);
        $image      = self::getImagePath($set_id, $filename, array('width' => $width, 'height' => $height, 'method' => $method));
        
        if( ! \SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $image) && \SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original)   )
        {
            $forceOriginalAspectRatio = $method == 'crop' ? false : true;
            $allowEnlargement = true;
            
            $thumb = new \SpoonThumbnail(FRONTEND_FILES_PATH . '/' . $original, $width, $height);
            $thumb->setAllowEnlargement($allowEnlargement);
            $thumb->setForceOriginalAspectRatio($forceOriginalAspectRatio);
            $thumb->parseToFile(FRONTEND_FILES_PATH . '/' . $image, 100);
        }

        return FRONTEND_FILES_URL . '/' . $image;
    }
}
