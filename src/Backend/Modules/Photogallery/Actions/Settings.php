<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;
use Backend\Modules\Photogallery\Engine\Api as Api;

/**
 * This settings action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Settings extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Loads the settings form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('settings');

        $depths = array_merge(
            array(null => BL::lbl('NotAllowed'), 0 => BL::lbl('infinity')),
            array_combine(range(1, 5), range(1, 5))
        );

        // categories depth
        $this->frm->addDropdown('categories_depth_start', array_combine(range(0, 5), range(0, 5)), BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth_start'));
        $this->frm->addDropdown('categories_depth', $depths, BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth'));

        // albums categories depth
        $this->frm->addDropdown('albums_categories_depth_start', array_combine(range(0, 5), range(0, 5)), BackendModel::getModuleSetting($this->URL->getModule(), 'albums_categories_depth_start'));
        $this->frm->addDropdown('albums_categories_depth', $depths, BackendModel::getModuleSetting($this->URL->getModule(), 'albums_categories_depth'));

        // general number of items
        $this->frm->addDropdown('general_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'general_number_of_items', 10));
        $this->frm->addCheckbox('specific_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'specific_number_of_items', false));
        // disable all paging checkboxes
        $this->frm->addCheckbox('no_specific_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_specific_number_of_items', false));
        
        // specific number of items
        $this->frm->addDropdown('overview_albums_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_albums_number_of_items', 10));
        $this->frm->addDropdown('overview_categories_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_categories_number_of_items', 10));
        $this->frm->addDropdown('related_list_categories_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_list_categories_number_of_items', 10));
        $this->frm->addDropdown('related_list_tags_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_list_tags_number_of_items', 10));
        $this->frm->addDropdown('related_categories_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_categories_number_of_items', 10));
        $this->frm->addDropdown('related_tags_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_tags_number_of_items', 10));
        // disable paging checkboxes
        $this->frm->addCheckbox('no_overview_albums_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_overview_albums_number_of_items', false));
        $this->frm->addCheckbox('no_overview_categories_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_overview_categories_number_of_items', false));
        $this->frm->addCheckbox('no_related_list_categories_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_related_list_categories_number_of_items', false));
        $this->frm->addCheckbox('no_related_list_tags_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_related_list_tags_number_of_items', false));
        $this->frm->addCheckbox('no_related_categories_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_related_categories_number_of_items', false));
        $this->frm->addCheckbox('no_related_tags_number_of_items', BackendModel::getModuleSetting($this->URL->getModule(), 'no_related_tags_number_of_items', false));        

        // add fields for SEO
        $this->frm->addCheckbox('ping_services', BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false));

        // add fields for RSS
        $this->frm->addCheckbox('rss_meta', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), true));
        $this->frm->addText('rss_title', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage()));
        $this->frm->addTextarea('rss_description', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage()));
        $this->frm->addText('feedburner_url', BackendModel::getModuleSetting($this->URL->getModule(), 'feedburner_url_' . BL::getWorkingLanguage()));
        
        // License
        $this->frm->addText('license_name', BackendModel::getModuleSetting($this->URL->getModule(), 'license_name'));
        $this->frm->addText('license_key', BackendModel::getModuleSetting($this->URL->getModule(), 'license_key'));
        $this->frm->addText('license_domain', BackendModel::getModuleSetting($this->URL->getModule(), 'license_domain'));

        // Watermark
        $this->frm->addCheckbox('allow_watermark', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_watermark', false));
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if($this->frm->isSubmitted())
        {
            // shorten fields
            $feedburnerURL = $this->frm->getField('feedburner_url');

            // validation
            $this->frm->getField('rss_title')->isFilled(BL::err('FieldIsRequired'));
                
            if($this->frm->getField('license_key')->isFilled())
            {
                $this->frm->getField('license_name')->isFilled(BL::err('FieldIsRequired'));
                $this->frm->getField('license_domain')->isURL(BL::err('InvalidURL'));
            }

            // feedburner URL is set
            if($feedburnerURL->isFilled())
            {
                // check if http:// is set and add if necessary
                $feedburner = !strstr($feedburnerURL->getValue(), 'http://') ? 'http://' . $feedburnerURL->getValue() : $feedburnerURL->getValue();
    
                // check if feedburner URL is valid
                if(!\SpoonFilter::isURL($feedburner)) $feedburnerURL->addError(BL::err('InvalidURL'));
            }

            // init variable
            else $feedburner = null;
            
            
            if($this->frm->isCorrect())
            {
                // set our settings
                //\Spoon::dump($this->frm->getField('categories_depth')->getValue());
                $selected_depth = $this->frm->getField('categories_depth')->getValue() != null ? $this->frm->getField('categories_depth')->getValue() : null;
                BackendModel::setModuleSetting($this->URL->getModule(), 'categories_depth_start', $this->frm->getField('categories_depth_start')->getValue());
                BackendModel::setModuleSetting($this->URL->getModule(), 'categories_depth', $selected_depth);

                BackendModel::setModuleSetting($this->URL->getModule(), 'general_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                BackendModel::setModuleSetting($this->URL->getModule(), 'specific_number_of_items', (bool) $this->frm->getField('specific_number_of_items')->getValue());
                if($this->frm->getField('specific_number_of_items')->getChecked())
                {
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_specific_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_overview_albums_number_of_items', (bool) $this->frm->getField('no_overview_albums_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_overview_categories_number_of_items', (bool) $this->frm->getField('no_overview_categories_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_list_categories_number_of_items', (bool) $this->frm->getField('no_related_list_categories_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_list_tags_number_of_items', (bool) $this->frm->getField('no_related_list_tags_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_categories_number_of_items', (bool) $this->frm->getField('no_related_categories_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_tags_number_of_items', (bool) $this->frm->getField('no_related_tags_number_of_items')->getValue());

                    BackendModel::setModuleSetting($this->URL->getModule(), 'overview_albums_number_of_items', (int) $this->frm->getField('overview_albums_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'overview_categories_number_of_items', (int) $this->frm->getField('overview_categories_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_list_categories_number_of_items', (int) $this->frm->getField('related_list_categories_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_list_tags_number_of_items', (int) $this->frm->getField('related_list_tags_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_categories_number_of_items', (int) $this->frm->getField('related_categories_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_tags_number_of_items', (int) $this->frm->getField('related_tags_number_of_items')->getValue());
                } else {
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_specific_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_overview_albums_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_overview_categories_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_list_categories_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_list_tags_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_categories_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'no_related_tags_number_of_items', (bool) $this->frm->getField('no_specific_number_of_items')->getValue());

                    BackendModel::setModuleSetting($this->URL->getModule(), 'overview_albums_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'overview_categories_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_list_categories_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_list_tags_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_categories_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                    BackendModel::setModuleSetting($this->URL->getModule(), 'related_tags_number_of_items', (int) $this->frm->getField('general_number_of_items')->getValue());
                }
                
                BackendModel::setModuleSetting($this->URL->getModule(), 'ping_services', (bool) $this->frm->getField('ping_services')->getValue());
                BackendModel::setModuleSetting($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
                BackendModel::setModuleSetting($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
                BackendModel::setModuleSetting($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), $this->frm->getField('rss_meta')->getValue());
                BackendModel::setModuleSetting($this->URL->getModule(), 'feedburner_url_' . BL::getWorkingLanguage(), $feedburner);
                
                $license_name = $this->frm->getField('license_name')->getValue();
                $license_key = $this->frm->getField('license_key')->getValue();
                $license_domain = $this->frm->getField('license_domain')->getValue();

                // watermark
                BackendModel::setModuleSetting($this->URL->getModule(), 'allow_watermark', (bool) $this->frm->getField('allow_watermark')->getValue());
                
                //if(!is_callable(array('Api', 'doCall'))) include dirname(__FILE__) . '/../engine/api_call.php';
                
                // If an ID exists of the API Call
                $api_call_id = BackendModel::getModuleSetting($this->URL->getModule(), 'api_call_id');
                if($api_call_id)
                {
                    // Only update when there is a license key
                    if($license_key)
                    {
                        // Only do call when values are changed
                        if(
                            $license_name != BackendModel::getModuleSetting($this->URL->getModule(), 'license_name') ||
                            $license_key != BackendModel::getModuleSetting($this->URL->getModule(), 'license_key') ||
                            $license_domain != BackendModel::getModuleSetting($this->URL->getModule(), 'license_domain')
                        )
                        {
                            // Update record
                            try
                            {
                            
                                // build parameters
                                $parameters = array(
                                    'license_name' => $license_name,
                                    'license_key' => $license_key,
                                    'license_domain' => $license_domain,
                                    'id' => $api_call_id
                                );
        
                                // call
                                $api = new Api();
                                $api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
                                $return = $api->doCall('products.updateProductInstallation', $parameters, false);
                        
                                // Only set the settings when a call happened
                                BackendModel::setModuleSetting($this->URL->getModule(), 'license_name', $license_name);
                                BackendModel::setModuleSetting($this->URL->getModule(), 'license_key', $license_key);
                                BackendModel::setModuleSetting($this->URL->getModule(), 'license_domain', $license_domain);
                            } 
                            catch(Exception $e) 
                            {
                            }
                        }
                    }
                }
                else
                {
                    // Insert record
                    try
                    {
                        // build parameters
                        $parameters = array(
                            'site_domain' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'fork.local',
                            'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
                            'type' => 'module',
                            'name' => 'photogallery',
                            'version' => '2.1',
                            'email' => \SpoonSession::get('email'),
                            'license_name' => $license_name,
                            'license_key' => $license_key,
                            'license_domain' => $license_domain
                        );
        
                        // call
                        $api = new Api();
                        $api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
                        $return = $api->doCall('photogallery.insertProductInstallation', $parameters, false);
                        $this->setSetting('photogallery', 'api_call_id', (string) $return->data->id);
                    } 
                    catch(Exception $e) 
                    {
                    }
                }

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
            }
        }
    }
}
