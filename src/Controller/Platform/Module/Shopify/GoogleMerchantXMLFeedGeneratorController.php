<?php

namespace App\Controller\Platform\Module\Shopify;

use App\Controller\Platform\_PlatformAbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GoogleMerchantXMLFeedGeneratorController extends _PlatformAbstractController
{
    public function __construct(
        private ShopifyController $shopifyController
    )
    {
        // do nothing
    }

    private function checkDirectory(): void
    {
        $dir = 'cdn';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }


    #[Route('/{_locale}/module/shopify/google-merchant-xml/get', name: 'admin_module_shopify_google_merchant_xml_get')]
    public function getGoogleMerchantXMLFeed(): Response
    {
        // check if public/cdn/google_merchant.xml exists, if yes, return it, if not, generate it with generateGoogleMerchantXMLFeed()
        if (file_exists('cdn/google-merchant-feed.xml') && filesize('cdn/google-merchant-feed.xml')>0) {
            $xml = file_get_contents('cdn/google-merchant-feed.xml');
            $response = new Response($xml);
            $response->headers->set('Content-Type', 'application/xml');

            return $response;
        }

        return $this->generateGoogleMerchantXMLFeed();
    }

    #[Route('/{_locale}/alma.xml', name: 'admin_module_shopify_order_listalma')]
    public function generateGoogleMerchantXMLFeed(): Response
    {
        $this->checkDirectory();
        //header("Access-Control-Allow-Origin: *");
        //header("Content-Type: text/xml; charset=UTF-8");

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $rss = $xml->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', 'http://base.google.com/ns/1.0');
        $xml->appendChild($rss);

        $channel = $xml->createElement('channel');
        $channel->appendChild($xml->createElement('title', 'PaperStories.hu'));
        $channel->appendChild($xml->createElement('description', 'Egyedi esküvői meghívók és köszönetkártyák'));
        $channel->appendChild($xml->createElement('link', 'https://www.paperstories.hu'));
        $rss->appendChild($channel);

        $requiredCollections = [
            'esküvői meghívó'       => 609770963272,
            'menükártya'            => 609771749704,
            'ültetőkártya'          => 609771782472,
            'köszönetkártya'        => 609772110152,
            'keresztelő meghívó'    => 609774141768,
        ];

        $g_availability = 'in_stock';
        $g_identifier_exists = 'no';
        $g_brand = 'Paper Stories';
        //$g_product_type = 'Wedding Ceremony Supplies';
        $g_google_product_category = 'Religious &amp; Ceremonial > Wedding Ceremony Supplies';

        foreach ($requiredCollections as $collectionName => $collectionId) {
            $products = $this->shopifyController->getProductsByCollectionId($collectionId, 250);

            if ($collectionName==='keresztelő meghívó') {
                $g_google_product_category = 'Religious &amp; Ceremonial > Religious Items';
            }

            foreach ($products as $product) {

                if ($product['status']!=='active') {
                    continue;
                }

                $collectionNamePrefix = '';
                $g_product_type = $product['product_type'];

                if ($product['product_type']==='eskuvoi meghivo') {
                    if (strpos($product['tags'], 'scodix-gold')!==false) {
                        $collectionNamePrefix = 'arany ';
                        //continue;
                    }
                }

                $productVariants = $this->shopifyController->getVariantsByProductId($product['id']);

                if (!empty($productVariants)) {
                    $productFirstVariant = $productVariants['0'];

                    if ($productVariants['0']['position']!==1) {
                        foreach ($productVariants as $productVariant) {
                            if ($productVariant['position']===1) {
                                $productFirstVariant = $productVariant;
                            }
                        }
                    }

                    if (array_key_exists(0, $product['images'])) {
                        $salePrice = null;
                        /*
                        if ($collectionName==='esküvői meghívó') {
                            $salePrice = number_format((floatval($productFirstVariant['price'])*0.80), 2);
                        }
                        */

                        $this->getXmlItem(
                            $xml,
                            $channel,
                            $productFirstVariant['sku'],
                            $product['title'].' '.$collectionNamePrefix.$collectionName.' '.$productFirstVariant['title'],
                            strip_tags($product['body_html']),
                            'https://www.paperstories.hu/products/'.$product['handle'],
                            $product['images']['0']['src'],
                            [],
                            $g_availability,
                            $productFirstVariant['price'],
                            $g_identifier_exists,
                            $g_brand,
                            $g_product_type,
                            $g_google_product_category,
                            $salePrice
                        );
                    } else {
                        var_dump($product);
                        exit;
                    }
                }
            }
        }

        header('Content-type: text/xml');
        $response = new Response($xml->saveXML());
        $response->headers->set('Content-Type', 'application/xml');

        // save to public/cdn/google-merchant-feed.xml
        $xml->save('cdn/google-merchant-feed.xml');

        // Save the XML feed to a file
        //$xml->asXML('google_merchant.xml');

        return $response;
    }

    private function getXmlItem($xml, $channel, $g_id, $g_title, $g_description, $g_link, $g_image_link, $g_additional_image_links, $g_availability, $g_price, $g_identifier_exists, $g_brand, $g_product_type, $g_google_product_category, $g_sale_price=null): void
    {
        $item = $xml->createElement('item');

        $item->appendChild($xml->createElement('g:id', $g_id));
        $title = $xml->createElement('g:title');
        $title->appendChild($xml->createCDATASection($g_title));
        $item->appendChild($title);

        $description = $xml->createElement('g:description');
        $description->appendChild($xml->createCDATASection($g_description));
        $item->appendChild($description);

        $link = $xml->createElement('g:link');
        $link->appendChild($xml->createCDATASection($g_link));
        $item->appendChild($link);

        $item->appendChild($xml->createElement('g:image_link', $g_image_link));

        foreach ($g_additional_image_links as $g_additional_image_link) {
            $item->appendChild($xml->createElement('g:additional_image_link', $g_additional_image_link));
        }
        $item->appendChild($xml->createElement('g:availability', $g_availability));
        $item->appendChild($xml->createElement('g:price', $g_price));

        if (!is_null($g_sale_price)) {
            $item->appendChild($xml->createElement('g:sale_price', $g_sale_price));
        }

        $item->appendChild($xml->createElement('g:product_type', $g_product_type));
        $item->appendChild($xml->createElement('g:identifier_exists', $g_identifier_exists));
        $item->appendChild($xml->createElement('g:brand', $g_brand));
        $item->appendChild($xml->createElement('g:google_product_category', $g_google_product_category));

        $channel->appendChild($item);
        unset($item);
    }
}
