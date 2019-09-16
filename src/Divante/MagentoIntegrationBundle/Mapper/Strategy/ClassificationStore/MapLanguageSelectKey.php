<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Tool;

/**
 * Class MapLanguageSelectKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapLanguageSelectKey extends MapSelectKey
{
    protected $countries;
    const ALLOWED_TYPES_ARRAY = ['language', 'languagemultiselect'];

    /**
     * @param KeyConfig $field
     * @return array
     * @throws \Exception
     */
    protected function getOptions(KeyConfig $field): array
    {
        $locales = Tool::getSupportedLocales();
        $languagesArray = [];
        foreach ($locales as $short => $translation) {
            $languagesArray[] = ['key' => $translation, 'value' => $short];
        }
        return $languagesArray;
    }
}
