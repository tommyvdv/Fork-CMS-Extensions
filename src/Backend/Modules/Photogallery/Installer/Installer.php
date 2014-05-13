<?php

namespace Backend\Modules\Photogallery\Installer;

use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Photogallery\Engine\Api as Api;

/**
 * Installer for the photogallery module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/data/install.sql');

        // add 'Photogallery' as a module
        $this->addModule('Photogallery', 'The multilingual photogallery with dynamic widgets.');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Photogallery');

        // action rights
        $this->setActionRights(1, 'Photogallery', 'Add');
        $this->setActionRights(1, 'Photogallery', 'AddCategory');
        $this->setActionRights(1, 'Photogallery', 'AddImagesChoose');
        $this->setActionRights(1, 'Photogallery', 'AddImagesExisting');
        $this->setActionRights(1, 'Photogallery', 'AddImagesUpload');
        $this->setActionRights(1, 'Photogallery', 'AddImagesUploadZip');
        $this->setActionRights(1, 'Photogallery', 'AddImagesUploadMultiple');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetCategories');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetChoose');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetLightbox');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetPaged');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetRelatedByCategories');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetRelatedByTags');
        $this->setActionRights(1, 'Photogallery', 'AddWidgetSlideshow');
        $this->setActionRights(1, 'Photogallery', 'Index');
        $this->setActionRights(1, 'Photogallery', 'Categories');
        $this->setActionRights(1, 'Photogallery', 'CategorySequence');
        $this->setActionRights(1, 'Photogallery', 'Delete');
        $this->setActionRights(1, 'Photogallery', 'DeleteCategory');
        $this->setActionRights(1, 'Photogallery', 'DeleteExtra');
        $this->setActionRights(1, 'Photogallery', 'DeleteImage');
        $this->setActionRights(1, 'Photogallery', 'Edit');
        $this->setActionRights(1, 'Photogallery', 'EditCategory');
        $this->setActionRights(1, 'Photogallery', 'EditImage');
        $this->setActionRights(1, 'Photogallery', 'EditModule');
        $this->setActionRights(1, 'Photogallery', 'EditWidgetCategories');
        $this->setActionRights(1, 'Photogallery', 'EditWidgetLightbox');
        $this->setActionRights(1, 'Photogallery', 'EditWidgetPaged');
        $this->setActionRights(1, 'Photogallery', 'EditWidgetRelatedByCategories');
        $this->setActionRights(1, 'Photogallery', 'EditWidgetRelatedByTags');
        $this->setActionRights(1, 'Photogallery', 'EditWidgetlideshow');
        $this->setActionRights(1, 'Photogallery', 'Extras');
        $this->setActionRights(1, 'Photogallery', 'MassAction');
        $this->setActionRights(1, 'Photogallery', 'ImagesSequence');
        $this->setActionRights(1, 'Photogallery', 'Sequence');
        $this->setActionRights(1, 'Photogallery', 'UploadImage');
        $this->setActionRights(1, 'Photogallery', 'Settings');

        // make module searchable
        $this->makeSearchable('Photogallery');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationId = $this->setNavigation($navigationModulesId, 'Photogallery', 'photogallery/index',  array(
            'photogallery/add_images_upload',
            'photogallery/add_images_upload_multiple',
            'photogallery/add_images_upload_zip',
            'photogallery/add_images_choose',
            'photogallery/add_images_existing',
            'photogallery/edit_image',
            'photogallery/add',
            'photogallery/edit',
        ));

        $this->setNavigation($navigationId, 'Albums', 'photogallery/index', array(
            'photogallery/index'
        ));

        $this->setNavigation($navigationId, 'Categories', 'photogallery/categories', array(
            'photogallery/add_category',
            'photogallery/edit_category'
        ));

        $this->setNavigation($navigationId, 'Extras', 'photogallery/extras', array(
            'photogallery/add_widget_choose',
            'photogallery/edit_widget_slideshow',
            'photogallery/edit_widget_lightbox',
            'photogallery/edit_widget_paged',
            'photogallery/edit_widget_categories',
            'photogallery/add_widget_categories',
            'photogallery/edit_block',
            'photogallery/add_widget_slideshow',
            'photogallery/add_widget_lightbox',
            'photogallery/add_widget_paged',
            'photogallery/edit_module',
            'photogallery/add_widget_related_by_categories',
            'photogallery/edit_widget_related_by_categories',
            'photogallery/add_widget_related_by_tags',
            'photogallery/edit_widget_related_by_tags',
        ));
        
        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Photogallery', 'photogallery/settings');
        
        // Settings
        $this->setSetting('Photogallery', 'awsAccessKey', '');
        $this->setSetting('Photogallery', 'awsSecretKey', '');
        $this->setSetting('Photogallery', 's3_url', '');
        $this->setSetting('Photogallery', 's3_account', false);
        $this->setSetting('Photogallery', 's3_region', '');
        
        // ping service (feedburner)
        $this->setSetting('Photogallery', 'ping_services', false);

        $db = $this->getDB();

        // Block
        $blockDataSettings = array(
                                    'show_close_button' => 'false',
                                    'show_arrows' => 'true',
                                    'show_caption' => 'true',
                                    'caption_type' => 'outside',
                                    'padding' =>  25,
                                    'margin' => 20,
                                    'modal' => 'false',
                                    'show_hover_icon' => 'true',
                                    'close_click' => 'false',
                                    'media_helper' => 'true',
                                    'navigation_effect' => 'none',
                                    'open_effect' => 'none',
                                    'close_effect' => 'none',
                                    'play_speed' => 3000,
                                    'loop' => 'true',
                                    'show_thumbnails' => 'true',
                                    'thumbnails_position' => 'bottom',
                                    'thumbnail_navigation_width' => 50,
                                    'thumbnail_navigation_height' => 50,
                                    'show_overlay' => 'true',
                                    'overlay_color' => 'rgba(255, 255, 255, 0.85)'
                            );

        $extraId = $db->insert('photogallery_extras', array('data' => serialize(array('action' => 'lightbox', 'display' => 'albums', 'settings' => $blockDataSettings)), 'action' => null, 'kind' => 'module', 'allow_delete' => 'N', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 1200, 'height' => 1200, 'method' => 'resize', 'kind' => 'large'));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 125, 'height' => 125, 'method' => 'crop', 'kind' => 'album_detail_overview_thumbnail'));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 200, 'height' => 200, 'method' => 'crop', 'kind' => 'album_overview_thumbnail'));
        
        // Module Extra
        $extraBlockId = $this->insertExtra('Photogallery', 'block', 'Photogallery', null, serialize(array('action' => 'lightbox', 'display' => 'albums', 'extra_id' => $extraId)));
        $this->insertExtra('Photogallery', 'block', 'Detail', 'Detail', serialize(array('action' => 'lightbox', 'display' => 'albums', 'extra_id' => $extraId)));
        $this->insertExtra('Photogallery', 'block', 'Category', 'Category', serialize(array('action' => 'lightbox', 'display' => 'albums', 'extra_id' => $extraId)));

        // Slideshow
        /*
        $extraId = $db->insert('photogallery_extras', array('action' => 'slideshow', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 600, 'height' => 350, 'method' => 'crop', 'kind' => 'large'));
        */
        
        // Lightbox
        /*
        $extraId = $db->insert('photogallery_extras', array('action' => 'lightbox', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 800, 'height' => 600, 'method' => 'resize', 'kind' => 'large'));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 75, 'height' => 75, 'method' => 'crop', 'kind' => 'thumbnail'));
        */

        // Paged
        /*
        $extraId = $db->insert('photogallery_extras', array('action' => 'paged', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 75, 'height' => 75, 'method' => 'crop', 'kind' => 'thumbnail'));
        */

        // Category widget
        /*
        $extraId = $db->insert('photogallery_extras', array('action' => 'categories', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
        $db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 500, 'height' => 350, 'method' => 'crop', 'kind' => 'large'));
        */
        
        // Widgets
        $this->insertExtra('Photogallery', 'widget', 'CategoryNavigation', 'CategoryNavigation');
        $this->insertExtra('Photogallery', 'widget', 'RelatedListByCategories', 'RelatedListByCategories');
        $this->insertExtra('Photogallery', 'widget', 'RelatedListByTags', 'RelatedListByTags');
        
        // loop languages
        foreach($this->getLanguages() as $language)
        {
            // feedburner URL
            $this->setSetting('Photogallery', 'feedburner_url_' . $language, '');

            // RSS settings
            $this->setSetting('Photogallery', 'rss_meta_' . $language, true);
            $this->setSetting('Photogallery', 'rss_title_' . $language, 'RSS');
            $this->setSetting('Photogallery', 'rss_description_' . $language, '');
        }
        
        // Insert page
        self::insertPhotogalleryPage('Photogallery', $extraBlockId );
        
        // Do API Call
        self::doApiCall();
    }

    private function insertPhotogalleryPage($title, $extraId)
    {
        // loop languages
        foreach($this->getLanguages() as $language)
        {
            
            // check if a page for blog already exists in this language
            if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
                                                FROM pages AS p
                                                INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                                                WHERE b.extra_id = ? AND p.language = ?',
                                                array($extraId, $language)))
            {
                $this->insertPage(
                    array('title' =>  $title, 'language' => $language, 'type' => 'root'),
                    null,
                    array('extra_id' => $extraId, 'position' => 'main')
                );
            }
        }
    }

    private function doApiCall()
    {
        //if(!is_callable(array('Api', 'doCall'))) include dirname(__FILE__) . '/../engine/api_call.php';
        
        try
        {
            // build parameters
            $parameters = array(
                'site_domain' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'fork.local',
                'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
                'type' => 'module',
                'name' => 'Photogallery',
                'version' => '3.1.3',
                'email' => \SpoonSession::get('email'),
                'license_name' => '',
                'license_key' => '',
                'license_domain' => ''
            );
        
            // call
            $api = new Api();
            $api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
            $return = $api->doCall('products.insertProductInstallation', $parameters, false);
            $this->setSetting('Photogallery', 'api_call_id', (string) $return->data->id);
        } 
        catch(Exception $e) 
        {}
    }
}
