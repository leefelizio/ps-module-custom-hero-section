<?php
/**
 * Copyright 2024 Lee Felizio Castro
 * @author    Lee Felizio Castro <feliziolee@gmail.com>
 * @copyright Since 2024 Lee Felizio Castro
 * @license   https://opensource.org/license/mit MIT LICENSE
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Lee_custom_hero_section extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        // module information
        $this->name = 'lee_custom_hero_section';
        $this->tab = 'front_office_features';
        $this->version = '1.3.0';
        $this->author = 'Lee Felizio Castro';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Lee Custom Hero Section', [], 'Modules.Banner.Admin');
        $this->description = $this->trans('Build a Custom Hero Section with eyebrow, title, subtitle and a call to action.', [], 'Modules.Banner.Admin');

        // minimum version Prestashop 8.0.2
        $this->ps_versions_compliancy = ['min' => '8.0.2', 'max' => _PS_VERSION_];

        $this->templateFile = 'module:lee_custom_hero_section/lee_custom_hero_section.tpl';
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionObjectLanguageAddAfter') &&
            $this->installFixtures() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayHeader');

    }

    // config custom css 
    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'lee_custom_hero_section-css',
            'modules/' . $this->name . '/css/lee_custom_hero_section.css'
        );
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        return $this->installFixture((int) $params['object']->id, Configuration::get('HERO_ICON', (int) Configuration::get('PS_LANG_DEFAULT')));
    }

    protected function installFixtures()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $this->installFixture((int) $lang['id_lang'], 'icon-hero-placeholder.png');
        }
        return true;
    }

    protected function installFixture($id_lang, $icon = null)
    {
        $values['HERO_ICON'][(int) $id_lang] = $icon;
        $values['HERO_EYEBROW'][(int) $id_lang] = '';
        $values['HERO_HEADING'][(int) $id_lang] = '';
        $values['HERO_SUBTITLE'][(int) $id_lang] = '';
        $values['HERO_BTN_LINK'][(int) $id_lang] = '';
        $values['HERO_BTN_TEXT'][(int) $id_lang] = '';

        Configuration::updateValue('HERO_ICON', $values['HERO_ICON']);
        Configuration::updateValue('HERO_EYEBROW', $values['HERO_EYEBROW']);
        Configuration::updateValue('HERO_HEADING', $values['HERO_HEADING']);
        Configuration::updateValue('HERO_SUBTITLE', $values['HERO_SUBTITLE']);
        Configuration::updateValue('HERO_BTN_LINK', $values['HERO_BTN_LINK']);
        Configuration::updateValue('HERO_BTN_TEXT', $values['HERO_BTN_TEXT']);
    }

    public function uninstall()
    {
        Configuration::deleteByName('HERO_ICON');
        Configuration::deleteByName('HERO_EYEBROW');
        Configuration::deleteByName('HERO_HEADING');
        Configuration::deleteByName('HERO_SUBTITLE');
        Configuration::deleteByName('HERO_BTN_LINK');
        Configuration::deleteByName('HERO_BTN_TEXT');

        return parent::uninstall();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitStoreConf')) {
            $languages = Language::getLanguages(false);
            $values = [];
            $update_icon_value = false;

            // config fields for all languages
            foreach ($languages as $lang) {
                if (isset($_FILES['HERO_ICON_' . $lang['id_lang']])
                    && isset($_FILES['HERO_ICON_' . $lang['id_lang']]['tmp_name'])
                    && !empty($_FILES['HERO_ICON_' . $lang['id_lang']]['tmp_name'])) {
                    if ($error = ImageManager::validateUpload($_FILES['HERO_ICON_' . $lang['id_lang']], 4000000)) {
                        return $this->displayError($error);
                    } else {
                        $ext = substr($_FILES['HERO_ICON_' . $lang['id_lang']]['name'], strrpos($_FILES['HERO_ICON_' . $lang['id_lang']]['name'], '.') + 1);
                        $file_name = md5($_FILES['HERO_ICON_' . $lang['id_lang']]['name']) . '.' . $ext;

                        if (!move_uploaded_file($_FILES['HERO_ICON_' . $lang['id_lang']]['tmp_name'], dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name)) {
                            return $this->displayError($this->trans('Error uploading the file.', [], 'Admin.Notifications.Error'));
                        } else {
                            if (Configuration::hasContext('HERO_ICON', $lang['id_lang'], Shop::getContext())
                                && Configuration::get('HERO_ICON', $lang['id_lang']) != $file_name) {
                                @unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . Configuration::get('HERO_ICON', $lang['id_lang']));
                            }
                            $values['HERO_ICON'][$lang['id_lang']] = $file_name;
                        }
                    }

                    $update_icon_value = true;
                }
                // recover field values for different languages
                $values['HERO_EYEBROW'][$lang['id_lang']] = Tools::getValue('HERO_EYEBROW_' . $lang['id_lang']);
                $values['HERO_HEADING'][$lang['id_lang']] = Tools::getValue('HERO_HEADING_' . $lang['id_lang']);
                $values['HERO_SUBTITLE'][$lang['id_lang']] = Tools::getValue('HERO_SUBTITLE_' . $lang['id_lang']);
                $values['HERO_BTN_LINK'][$lang['id_lang']] = Tools::getValue('HERO_BTN_LINK_' . $lang['id_lang']);
                $values['HERO_BTN_TEXT'][$lang['id_lang']] = Tools::getValue('HERO_BTN_TEXT_' . $lang['id_lang']);
            }

            if ($update_icon_value && isset($values['HERO_ICON'])) {
                Configuration::updateValue('HERO_ICON', $values['HERO_ICON']);
            }

            // update field values
            Configuration::updateValue('HERO_EYEBROW', $values['HERO_EYEBROW']);
            Configuration::updateValue('HERO_HEADING', $values['HERO_HEADING']);
            Configuration::updateValue('HERO_SUBTITLE', $values['HERO_SUBTITLE']);
            Configuration::updateValue('HERO_BTN_LINK', $values['HERO_BTN_LINK']);
            Configuration::updateValue('HERO_BTN_TEXT', $values['HERO_BTN_TEXT']);

            // clear cache
            $this->_clearCache($this->templateFile);
            return $this->displayConfirmation($this->trans('The Custom Hero Banner is now updated.', [], 'Admin.Notifications.Success'));
        }
        return '';
    }

    public function getContent()
    {
        return $this->postProcess() . $this->renderForm();
    }

    // rendering back office form hero fields with details
    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Custom Hero Section > Settings', [], 'Admin.Global'),
                    'icon' => 'icon-star',
                ],
                'input' => [
                    [
                        'type' => 'file_lang',
                        'label' => $this->trans('Icon', [], 'Modules.Banner.Admin'),
                        'name' => 'HERO_ICON',
                        'desc' => $this->trans('Icon - Please upload your hero icon.', [], 'Modules.Banner.Admin'),
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Eyebrow', [], 'Modules.Banner.Admin'),
                        'name' => 'HERO_EYEBROW',
                        'desc' => $this->trans('Eyebrow - Please enter a short but meaningful eyebrow for the Hero Section.', [], 'Modules.Banner.Admin'),
                    ], 
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Heading', [], 'Modules.Banner.Admin'),
                        'name' => 'HERO_HEADING',
                        'desc' => $this->trans('Heading - Please enter a short but meaningful heading for the Hero Section.', [], 'Modules.Banner.Admin'),
                    ],                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Subtitle', [], 'Modules.Banner.Admin'),
                        'name' => 'HERO_SUBTITLE',
                        'desc' => $this->trans('Subtitle - Please enter a short but meaningful subtitle for the Hero Section.', [], 'Modules.Banner.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Call To Action link', [], 'Modules.Banner.Admin'),
                        'name' => 'HERO_BTN_LINK',
                        'desc' => $this->trans('CTA Link - Please insert the URL for the hero call to action.', [], 'Modules.Banner.Admin'),
                    ],
                   [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Call To Action text', [], 'Modules.Banner.Admin'),
                        'name' => 'HERO_BTN_TEXT',
                        'desc' => $this->trans('CTA text - Please enter a short text for the Hero Section call to action', [], 'Modules.Banner.Admin'),
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitStoreConf';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(false);
        $fields = [];

        foreach ($languages as $lang) {
            $fields['HERO_ICON'][$lang['id_lang']] = Tools::getValue('HERO_ICON_' . $lang['id_lang'], Configuration::get('HERO_ICON', $lang['id_lang']));
            $fields['HERO_EYEBROW'][$lang['id_lang']] = Tools::getValue('HERO_EYEBROW' . $lang['id_lang'], Configuration::get('HERO_EYEBROW', $lang['id_lang']));
            $fields['HERO_HEADING'][$lang['id_lang']] = Tools::getValue('HERO_HEADING' . $lang['id_lang'], Configuration::get('HERO_HEADING', $lang['id_lang']));
            $fields['HERO_SUBTITLE'][$lang['id_lang']] = Tools::getValue('HERO_SUBTITLE' . $lang['id_lang'], Configuration::get('HERO_SUBTITLE', $lang['id_lang']));
            $fields['HERO_BTN_LINK'][$lang['id_lang']] = Tools::getValue('HERO_BTN_LINK' . $lang['id_lang'], Configuration::get('HERO_BTN_LINK', $lang['id_lang']));
            $fields['HERO_BTN_TEXT'][$lang['id_lang']] = Tools::getValue('HERO_BTN_TEXT' . $lang['id_lang'], Configuration::get('HERO_BTN_TEXT', $lang['id_lang']));
        }

        return $fields;
    }

    // get cached widget if it is already stored 
    public function renderWidget($hookName, array $params)
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('lee_custom_hero_section','modules/'.$this->name.'lee_custom_hero_section'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        }
        return $this->fetch($this->templateFile, $this->getCacheId('lee_custom_hero_section'));
    }

    public function getWidgetVariables($hookName, array $params)
    {
        $iconame = Configuration::get('HERO_ICON', $this->context->language->id);
        $iconDir = _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $iconame;

        if ($iconame && file_exists($iconDir)) {
            $sizes = getimagesize($iconDir);
            $this->smarty->assign([
                'hero_icon' => $this->context->link->protocol_content . Tools::getMediaServer($iconame) . $this->_path . 'img/' . $iconame,
                'hero_width' => $sizes[0],
                'hero_height' => $sizes[1],
            ]);
        }

        $hero_btn_link = Configuration::get('HERO_BTN_LINK', $this->context->language->id);
        if (!$hero_btn_link) {
            $hero_btn_link = $this->context->link->getPageLink('index');
        }

        return [
            'hero_eyebrow' => Configuration::get('HERO_EYEBROW', $this->context->language->id),
            'hero_heading' => Configuration::get('HERO_HEADING', $this->context->language->id),
            'hero_subtitle' => Configuration::get('HERO_SUBTITLE', $this->context->language->id),
            'hero_btn_link' => $this->updateUrl($hero_btn_link),
            'hero_btn_text' => Configuration::get('HERO_BTN_TEXT', $this->context->language->id),

        ];
    }

    // force https in case of different protocol
    private function updateUrl($link)
    {
        if (substr($link, 0, 7) !== 'http://' && substr($link, 0, 8) !== 'https://') {
            $link = 'https://'.$link;
        }

        return $link;
    }
}
