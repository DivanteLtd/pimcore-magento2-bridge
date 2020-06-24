<?php

namespace Divante\MagentoIntegrationBundle\Application\Asset;

use Pimcore\Model\Asset;
use Pimcore\Model\Webservice\Data\Mapper;

/**
 * Class MappedAssetService
 * @package Divante\MagentoIntegrationBundle\Application\Asset
 */
class MappedAssetService
{
    /**
     * @param string $id
     * @param string|null $thumbnail
     * @return array
     * @throws \Exception
     */
    public function getAsset(string $id, ?string $thumbnail): array
    {
        $asset = Asset::getById($id);
        if (!$asset instanceof Asset) {
            [
                "success" => false,
                "message" => sprintf("Asset with id: %s not found", $id)
            ];
        }

        $outputAsset = Mapper::map($asset, "Pimcore\Model\Webservice\Data\Asset\File\Out", 'out');
        if ($thumbnail && $asset instanceof Asset\Image) {
            $thumbnail = $asset->getThumbnail($thumbnail, false);
            $thumbnailData = file_get_contents("http://localhost" . (string) $thumbnail);

            $thumbnailOutput = [];
            $thumbnailOutput['data'] = base64_encode($thumbnailData);
            $thumbnailOutput['mimetype'] = $thumbnail->getMimeType();
            $outputAsset->{"thumbnail"} = $thumbnailOutput;
        }

        return ['data' => $outputAsset];
    }
}
