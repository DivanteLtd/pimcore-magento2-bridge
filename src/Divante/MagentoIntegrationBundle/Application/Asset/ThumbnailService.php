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
     * @return string
     */
    public function getThumbnailData(Asset $asset, string $thumbnail): string
    {
        $thumbnail = $asset->getThumbnail($thumbnail, false);
        $thumbnailData = file_get_contents(
            sprintf(
                "%s://%s%s",
                $this->router->getContext()->getScheme(),
                $this->router->getContext()->getHost(),
                (string) $thumbnail
            ));

        return base64_encode($thumbnailData);
    }
}
