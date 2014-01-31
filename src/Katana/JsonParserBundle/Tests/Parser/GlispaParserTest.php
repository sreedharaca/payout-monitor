<?php

namespace Katana\JsonParserBundle\Tests\Parser;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\JsonParserBundle\Service\ParserManager;


class GlispaParserTest extends WebTestCase
{
    private $container;

    public function testGlispaParser()
    {
        $xml = '<data lastUpdate="" hash="605e77de4a9c4ab8c51e98a962ff32bd">
<campaign glispaID="18759" name="Aviasales_iOS_iPad_Russia">
<category>MOBILE: Utility App</category>
<countries>RU</countries>
<summary>Aviasales is a flight ticket search engine filtering the best-priced tickets  via such websites as OneTwoTrip, Ozon.travel, Eviterra, Tickets.ru, Sindbad, Senturia, Trip.ru, Svyaznoy.Travel, Biletix, AnyWayAnyDay, BiletDV, Tripsta, Airtickets, KupiBilet, Clickavia, Wegolo, Nabortu, Chabooka, Agent.ru and dozens of other popular Russian and European online-agencies and airlines.
</summary>
<acquisition>CPI (Download, Install &amp; Open)</acquisition>
<target></target>
<rules>Absolutely no Incentivization, No Brand bidding, No Adult Content, Own Creatives need Approval, No Gateway/Content Lock Traffic</rules>
<tracking></tracking>
<payout>1.90 USD</payout>
<creativeURL>http://creatives.glispa.com/AviaSales/</creativeURL>
<creatives>
</creatives>
</campaign>
</data>';

        $ParserManager = $this->container->get('parser_manager');

        $Parser = $ParserManager->getParser(ParserManager::PROVIDER_GLISPA);

        $data = $Parser->parse($xml);

        $offer = $data[0];

        foreach($offer['countries'] as $country){
            $country_codes[] = $country->getCode();
        }

        $this->assertTrue($offer['external_id'] == 18759, 'Wrong offer id.');
        $this->assertTrue($offer['name'] == "Aviasales_iOS_iPad_Russia", 'Wrong name.');
        $this->assertTrue($offer['payout'] == 1.90, 'Offer name must be not empty.');
        $this->assertTrue($offer['preview_url'] == '', 'Wrong preview_url got.');
        $this->assertEquals(1, count($offer['countries']), 'Wrong country count.');
        $this->assertTrue(in_array('RU', $country_codes), 'Country code must exist in array.');
    }

    public function testParsePayout()
    {
        $ParserManager = $this->container->get('parser_manager');

        $Parser = $ParserManager->getParser(ParserManager::PROVIDER_GLISPA);

        $payout = $Parser->parsePayout("0.30 USD");

        $this->assertEquals(0.30, $payout, 'Payout does not equals.');
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
