<?php


namespace Divante\MagentoIntegrationBundle\Application\Asset;

use Pimcore\Model\Asset;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ThumbnailService
 * @package Divante\MagentoIntegrationBundle\Application\Asset
 */
class ThumbnailService
{
    const HASH_ALGO = "sha1";

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
     * @param Asset $asset
     * @param string $thumbnail
     * @return array
     */
    public function getThumbnailData(Asset $asset, string $thumbnail): array
    {
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
        $checksum = hash(static::HASH_ALGO, $thumbnailData);
        $thumbnailOutput["checksum"] = [
            'algo' => static::HASH_ALGO,
            'value' => $checksum
        ];

        return  $thumbnailOutput;
    }
}
