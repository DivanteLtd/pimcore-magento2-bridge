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
    const HASH_ALGO = "sha1";

    /**
     * @var ThumbnailService
     */
    protected $thumbnailService;

    /**
     * MappedAssetService constructor.
     * @param ThumbnailService $thumbnailService
     */
    public function __construct(ThumbnailService $thumbnailService)
    {
        $this->thumbnailService = $thumbnailService;
    }

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
            return [
                "success" => false,
                "message" => sprintf("Asset with id: %s not found", $id)
            ];
        }

        $outputAsset = Mapper::map($asset, "Pimcore\Model\Webservice\Data\Asset\File\Out", 'out');
        if ($thumbnail && $asset instanceof Asset\Image) {
            try {
                $outputAsset->{"thumbnail"} = $this->thumbnailService->getThumbnailData($asset, $thumbnail);
            } catch (\Exception $exception) {
                return [
                    "success" => false,
                    "message" => sprintf(
                        "Error retrieving thumbnail data from asset: %s, thumbnail: %s",
                        $id,
                        $thumbnail
                    )
                ];
            }
        }

        $outputAsset->checksum = [
            'algo' => static::HASH_ALGO,
            'value' => $asset->getChecksum(static::HASH_ALGO)
        ];

        return ['data' => $outputAsset];
    }
}
