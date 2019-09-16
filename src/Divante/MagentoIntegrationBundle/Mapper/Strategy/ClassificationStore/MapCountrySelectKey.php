<?php
/**
 * @category    pimcore
 * @date        20/07/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore;

use Pimcore\Localization\LocaleService;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MapCountrySelectKey
 * @package Divante\MagentoIntegrationBundle\Mapper\Strategy\ClassificationStore
 */
class MapCountrySelectKey extends MapSelectKey
{
    protected $countries;
    const ALLOWED_TYPES_ARRAY = ['country', 'countrymultiselect'];

    /**
     * MapCountrySelectKey constructor.
     * @param TranslatorInterface $translator
     * @param LocaleService       $locale
     */
    public function __construct(TranslatorInterface $translator, LocaleService $locale)
    {
        $this->countries = $locale->getDisplayRegions();
        parent::__construct($translator);
    }

    /**
     * @param KeyConfig $field
     * @return array
     */
    protected function getOptions(KeyConfig $field): array
    {
        $countriesArray = [];
        foreach ($this->countries as $shortName => $longName) {
            $countriesArray[] = ['key' => $longName, 'value' => $shortName];
        }
        return $countriesArray;
    }
}
