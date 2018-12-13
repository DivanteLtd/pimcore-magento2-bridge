<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        15/06/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data;

use Pimcore\Model;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields as LocalizedfieldsParent;
use Pimcore\Tool;

/**
 * Class Localizedfields
 * @package Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data
 */
class Localizedfields
{
    /** @var LocalizedfieldsParent  */
    protected $field;

    /**
     * Localizedfields constructor.
     * @param LocalizedfieldsParent $field
     */
    public function __construct(LocalizedfieldsParent $field)
    {
        $this->field = $field;
    }

    /**
     * This method is copied from Picmore core
     * @inheritdoc
     */
    public function getForWebserviceExport($object, $params = [])
    {
        $wsData = [];
        $user   = Tool\Admin::getCurrentUser();
        $languagesAllowed = null;
        if ($user && !$user->isAdmin()) {
            $languagesAllowed = DataObject\Service::getLanguagePermissions($object, $user, 'lView');

            if ($languagesAllowed) {
                $languagesAllowed = array_keys($languagesAllowed);
            }
        }

        $validLanguages = Tool::getValidLanguages();
        $localeService  = \Pimcore::getContainer()->get('pimcore.locale');
        $localeBackup   = $localeService->getLocale();

        if (!$validLanguages) {
            return $wsData;
        }
        foreach ($validLanguages as $language) {
            foreach ($this->field->getFieldDefinitions() as $fd) {
                if ($languagesAllowed && !in_array($language, $languagesAllowed)) {
                    continue;
                }

                $localeService->setLocale($language);

                $params['locale'] = $language;

                $el = new Model\Webservice\Data\DataObject\Element();
                $el->name  = $fd->getName();
                $el->type  = $fd->getFieldType();
                $el->label = $fd->getTitle();
                $el->value = $fd->getForWebserviceExport($object, $params);
                if ($el->value == null && $this->field::getDropNullValues()) {
                    continue;
                }
                $el->language = $language;
                $wsData[] = $el;
            }
        }

        $localeService->setLocale($localeBackup);

        return $wsData;
    }
}
