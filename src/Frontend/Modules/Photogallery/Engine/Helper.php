<?php

namespace Frontend\Modules\Photogallery\Engine;

use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendURL;
use Frontend\Core\Engine\Theme as FrontendTheme;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;

/**
 * In this file we store all generic functions that we will be using in the photogallery module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Helper 
{
    /**
     * The keys an structural data for pages
     *
     * @var    array
     */
    private static $resolution = array();
    private static $generated_images = array();

    public static function mapModifiers($tpl)
    {
        // parse image methods
        $tpl->mapModifier('createimageresolution', array('Frontend\Modules\Photogallery\Engine\Helper', 'createImageResolution'));
    }

    public static function getResolutions($language = null)
    {
        // redefine
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

        // do the keys exists in the cache?
        if (!isset(self::$resolution[$language]) || empty(self::$resolution[$language])) {
            // validate file @later: the file should be regenerated
            if (!is_file(FRONTEND_CACHE_PATH . '/Photogallery/resolution_' . $language . '.php')) {
                throw new Exception('No navigation-file (navigation_' . $language . '.php) found.');
            }

            // init var
            $resolution = array();

            // require file
            require FRONTEND_CACHE_PATH . '/Photogallery/resolution_' . $language . '.php';

            // store
            self::$resolution[$language] = $resolution;
        }

        // also get the generated images
        self::getGeneratedImages($language);

        // return from cache
        return self::$resolution[$language];
    }

    public static function getGeneratedImages($language = null)
    {
        // redefine
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

        // do the keys exists in the cache?
        if (!isset(self::$generated_images[$language]) || empty(self::$generated_images[$language])) {
            // validate file @later: the file should be regenerated
            if (!is_file(FRONTEND_CACHE_PATH . '/Photogallery/generated_images_' . $language . '.php')) {
                throw new Exception('No navigation-file (navigation_' . $language . '.php) found.');
            }

            // init var
            $generated_images = array();

            // require file
            require FRONTEND_CACHE_PATH . '/Photogallery/generated_images_' . $language . '.php';

            // store
            self::$generated_images[$language] = $generated_images;
        }

        // return from cache
        return self::$generated_images[$language];
    }

    public static function createImageResolution($var, $set_id, $filename, $kind = 'backend_thumb', $dir = 'photogallery', $watermark = false, $force_regeneration = false)
    {
        // make sure the resolutions are in here
        self::getResolutions();

        // get the resolution
        $resolution = self::getResolution($kind);
        if(!$resolution) return false;
        $width = $resolution['width']; $height = $resolution['height']; $method = $resolution['method']; $watermark = $resolution['watermark'];
//\Spoon::dump(FRONTEND_FILES_PATH . '/photogallery/watermarks/source/' . ($watermark ? $watermark : $resolution['watermark']));
        $watermark = \SpoonFile::exists(FRONTEND_FILES_PATH . '/photogallery/watermarks/source/' . ($watermark ? $watermark : $resolution['watermark'])) ? (FRONTEND_FILES_PATH . '/photogallery/watermarks/source/' . ($watermark ? $watermark : $resolution['watermark'])) : null;
        $allow_watermark = $resolution['allow_watermark'] == 'Y';
        $watermark_position = self::translatePosition($resolution['watermark_position']);
        $watermark_padding = (int) $resolution['watermark_padding'];

        //\Spoon::dump($watermark);

        $original   = self::getOriginalPath($set_id, $filename, $dir);
        $image      = self::getImagePath($set_id, $filename, array('width' => $width, 'height' => $height, 'method' => $method), $dir);
        
        //Spoon::dump(SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original));
//Spoon::dump($original);
        //if($resolution['regenerate'] == 'Y')
            //Spoon::dump($dir);

        if(self::hasResolutionBeenUpdatedSinceImageGeneration($set_id, $filename, $kind, $resolution['edited_on_unix'])) $force_regeneration = true;

        if(
            (!\SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $image) && \SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original)) ||
            $resolution['regenerate'] == 'Y' ||
            $force_regeneration
        )
        {
            //if($resolution['regenerate'] == 'Y') self::updateResolution(array('regenerate' => 'N', 'id' => $resolution['id']));

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

            self::updateImageGenerationInformation($set_id, $filename, $kind);
        }

        return FRONTEND_FILES_URL . '/' . $image . ($resolution['edited_on_unix'] ? '?unix=' . $resolution['edited_on_unix'] : '');
    }

    public static function hasResolutionBeenUpdatedSinceImageGeneration($image_set_id, $image_filename, $resolution_kind, $edited_on_unix)
    {
        //return true;

        if(isset(self::$generated_images[FRONTEND_LANGUAGE][$resolution_kind][$image_set_id][$image_filename]))
        {
            if(self::$generated_images[FRONTEND_LANGUAGE][$resolution_kind][$image_set_id][$image_filename]['generated_on_unix'] < $edited_on_unix)
            {
                set_time_limit(0);
                return true;
            }
        }

        else return false;
/*
        return (bool) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
            FROM photogallery_resolutions_images AS i
            WHERE image_set_id = ?
                AND image_filename = ? #"1401974635_grass-blades.jpg"
                AND resolution_kind =  ?#"deleteable"
                AND UNIX_TIMESTAMP(generated_on) < ? #1401969055',
            array(
                (int) $image_set_id,
                $image_filename,
                $resolution_kind,
                $edited_on_unix
            )
        );
*/
    }

    public static function updateImageGenerationInformation($image_set_id, $image_filename, $resolution_kind)
    {
        FrontendModel::get('database')->execute(
            'INSERT INTO photogallery_resolutions_images (image_set_id, image_filename, resolution_kind, generated_on) VALUES ('.
                $image_set_id . ',"' . $image_filename . '","' . $resolution_kind . '","' . FrontendModel::getUTCDate() . '")'
            .' ON DUPLICATE KEY UPDATE generated_on = "' . FrontendModel::getUTCDate() . '"'
        );

        // build resolutions cache
        BackendPhotogalleryModel::buildResolutionCache(FRONTEND_LANGUAGE);
    }

    public static function getResolution($kind)
    {
        if(isset(self::$resolution[FRONTEND_LANGUAGE][$kind])) return self::$resolution[FRONTEND_LANGUAGE][$kind];
/*
        return (array) FrontendModel::get('database')->getRecord(
            'SELECT *,
                IF(width_null = "Y", NULL, width) AS width,
                IF(height_null = "Y", NULL, height) AS height,
                IF(watermark IS NULL, NULL, CONCAT("/products/watermarks/source/", watermark)) AS watermark,
                UNIX_TIMESTAMP(edited_on) AS edited_on_unix
            FROM photogallery_resolutions
            WHERE kind = ?',
            $kind,
            'id'
        );
*/
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
