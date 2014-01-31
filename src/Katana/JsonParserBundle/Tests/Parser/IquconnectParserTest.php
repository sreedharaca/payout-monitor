<?php

namespace Katana\JsonParserBundle\Tests\Parser;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\JsonParserBundle\Service\ParserManager;


class IquconnectParserTest extends WebTestCase
{
    private $container;

    public function testParse()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<offer_feed_response xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://cakemarketing.com/affiliates/api/4/">
  <success>true</success>
  <row_count>24</row_count>
  <offers>
    <offer>
      <offer_id>169</offer_id>
      <offer_contract_id>196</offer_contract_id>
      <campaign_id xsi:nil="true" />
      <offer_name>Eternal Uprising ( android) </offer_name>
      <vertical_name>Mobile</vertical_name>
      <offer_status>
        <status_id>2</status_id>
        <status_name>Public</status_name>
      </offer_status>
      <payout>$2.50</payout>
      <price_format>CPA</price_format>
      <preview_link />
      <thumbnail_image_url />
      <description />
      <restrictions />
      <allowed_countries>
        <country>
          <country_code>AU</country_code>
          <country_name>Australia</country_name>
        </country>
        <country>
          <country_code>CA</country_code>
          <country_name>Canada</country_name>
        </country>
        <country>
          <country_code>GB</country_code>
          <country_name>United Kingdom</country_name>
        </country>
        <country>
          <country_code>NL</country_code>
          <country_name>Netherlands</country_name>
        </country>
        <country>
          <country_code>US</country_code>
          <country_name>United States</country_name>
        </country>
      </allowed_countries>
      <allowed_media_types />
      <expiration_date xsi:nil="true" />
      <tags />
      <advertiser_extended_terms />
      <hidden>false</hidden>
    </offer>
    <offer>
      <offer_id>158</offer_id>
      <offer_contract_id>185</offer_contract_id>
      <campaign_id xsi:nil="true" />
      <offer_name>Eternal Uprising iOS</offer_name>
      <vertical_name>Mobile</vertical_name>
      <offer_status>
        <status_id>2</status_id>
        <status_name>Public</status_name>
      </offer_status>
      <payout>$3.00</payout>
      <price_format>CPA</price_format>
      <preview_link />
      <thumbnail_image_url />
      <description />
      <restrictions>No Incent,</restrictions>
      <allowed_countries>
        <country>
          <country_code>AU</country_code>
          <country_name>Australia</country_name>
        </country>
        <country>
          <country_code>CA</country_code>
          <country_name>Canada</country_name>
        </country>
        <country>
          <country_code>GB</country_code>
          <country_name>United Kingdom</country_name>
        </country>
        <country>
          <country_code>US</country_code>
          <country_name>United States</country_name>
        </country>
      </allowed_countries>
      <allowed_media_types />
      <expiration_date xsi:nil="true" />
      <tags />
      <advertiser_extended_terms>Target: iOS 6.0 + ( no Ipods ) </advertiser_extended_terms>
      <hidden>false</hidden>
    </offer>
  </offers>
</offer_feed_response>
XML;

        $ParserManager = $this->container->get('parser_manager');

        $Parser = $ParserManager->getParser(ParserManager::PROVIDER_IQUCONNECT);

        $data = $Parser->parse($xml);

        $offer = $data[0];


        $this->assertCount(2, $data, 'Wrong number of parsed offers.');
        $this->assertTrue($offer['external_id'] == 169, 'Wrong offer id.');
        $this->assertTrue($offer['name'] == "Eternal Uprising ( android) ", 'Wrong name.');
        $this->assertTrue($offer['payout'] == 2.50, 'Offer name must be not empty.');
        $this->assertTrue($offer['preview_url'] == '', 'Wrong preview_url got.');
        $this->assertEquals(5, count($offer['countries']), 'Wrong country count.');
    }

    /***
     * Set Up container
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();
    }
}
