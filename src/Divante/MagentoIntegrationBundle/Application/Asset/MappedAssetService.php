<?php

namespace Divante\MagentoIntegrationBundle\Application\Asset;

use Pimcore\Model\Asset;
use Pimcore\Model\Webservice\Data\Mapper;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MappedAssetService
 * @package Divante\MagentoIntegrationBundle\Application\Asset
 */
class MappedAssetService
{

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * MappedAssetService constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
            [
                "success" => false,
                "message" => sprintf("Asset with id: %s not found", $id)
            ];
        }

        $algo = 'sha1';
        $outputAsset = Mapper::map($asset, "Pimcore\Model\Webservice\Data\Asset\File\Out", 'out');
        if ($thumbnail && $asset instanceof Asset\Image) {
            $thumbnail = $asset->getThumbnail($thumbnail, false);
            $thumbnailData = file_get_contents(
                sprintf(
                    "%s://%s%s",
                    $this->router->getContext()->getScheme(),
                    $this->router->getContext()->getHost(),
                    (string) $thumbnail
                ));

            $thumbnailOutput = [];
            $thumbnailOutput['data'] = base64_encode($thumbnailData);
            $thumbnailOutput['mimetype'] = $thumbnail->getMimeType();
            $checksum = hash($algo, $thumbnailData);
            $thumbnailOutput["checksum"] = [
                'algo' => $algo,
                'value' => $checksum
            ];
            $outputAsset->{"thumbnail"} = $thumbnailOutput;
        }


        $outputAsset->checksum = [
            'algo' => $algo,
            'value' => $asset->getChecksum($algo)
        ];

        return ['data' => $outputAsset];
    }
}
