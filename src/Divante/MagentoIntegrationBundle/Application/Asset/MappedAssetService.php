<?php

namespace Divante\MagentoIntegrationBundle\Application\Asset;

use Divante\MagentoIntegrationBundle\Domain\DataObject\IntegrationConfiguration\AttributeType;
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
     * @param string $idAsset
     * @param string|null $thumbnail
     * @return array
     * @throws \Exception
     */
    public function getAsset(string $idAsset): array
    {
        $params = explode(AttributeType::THUMBNAIL_CONCAT, $idAsset);
        $idAsset = $params[0];
        $thumbnail = $params[1];
        $asset = Asset::getById($idAsset);
        if (!$asset instanceof Asset) {
            return [
                "success" => false,
                "message" => sprintf("Asset with id: %s not found", $idAsset)
            ];
        }

        $outputAsset = Mapper::map($asset, "Pimcore\Model\Webservice\Data\Asset\File\Out", 'out');
        $checksum = $asset->getChecksum(static::HASH_ALGO);
        if ($thumbnail && $thumbnail !== AttributeType::IMAGE_DEFAULT && $asset instanceof Asset\Image) {
            try {
                $outputAsset->data = $this->thumbnailService->getThumbnailData($asset, $thumbnail);
                $outputAsset->mimetype = $asset->getThumbnail($thumbnail)->getMimeType();
                $checksum = $asset->getThumbnail($thumbnail)->getChecksum(static::HASH_ALGO);
            } catch (\Exception $exception) {
                return [
                    "success" => false,
                    "message" => sprintf(
                        "Error retrieving thumbnail data from asset: %s, thumbnail: %s",
                        $idAsset,
                        $thumbnail
                    )
                ];
            }
        }

        $outputAsset->checksum = [
            'algo' => static::HASH_ALGO,
            'value' => $checksum
        ];

        return [
            'success' => true,
            'data' => $outputAsset
        ];
    }
}
