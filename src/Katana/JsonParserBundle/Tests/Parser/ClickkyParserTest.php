<?php

namespace Katana\JsonParserBundle\Tests\Parser;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\JsonParserBundle\Service\ParserManager;


class ClickkyParserTest extends WebTestCase
{
    private $container;

    public function testParse()
    {
        $json = <<<'JSON'
{
  "status": "ok",
  "available": 518,
  "pageindex": 1,
  "pagecount": 1,
  "count": 518,
  "offers": [
    {
      "offer_id": 55120,
      "offer_type": "install",
      "offer_model": "CPI",
      "traffic_type": "non incentive",
      "free": "true",
      "app_id": "id645949180",
      "name": "Jelly Splash iPhone\/iPad",
      "description": "Connect colorful lines of Jelly to solve many fun puzzles!",
      "instructions": "Please install app and open it",
      "link": "http:\/\/cpactions.com\/offer\/3795\/55120",
      "icon": "http:\/\/n2.clickky.biz\/storage\/files\/c1\/u1001908\/banners\/90-90jelly_copy2.png",
      "payout": 2.1,
      "targeting": {
        "os": [
          "iPhone OS",
          "iPad OS"
        ],
        "countries": [
          "DE",
          "GB",
          "US"
        ]
      }
    },
    {
      "offer_id": 55119,
      "offer_type": "install",
      "offer_model": "CPI",
      "traffic_type": "non incentive",
      "free": "true",
      "app_id": "id523660889",
      "name": "Total Domination US UK AU CA iPad (Video 3)",
      "description": "The best MMO strategy game is now available for iOS. Over 30 million installs on the web \u2013 Total Domination\u2122: Reborn is a standalone mobile game completely independent from the web version.\r\n\r\n\r\nCivilization has fallen, and war is now the only world-order. Rise from the ashes of the wasteland, take command, form your clan and begin your quest towards Total Domination! \r\n\r\nFor the first time, the popular Total Domination\u2122 franchise has been extended to iOS as a standalone epic adventure, featuring all new groundbreaking graphics and gameplay.\r\n",
      "instructions": "Install the application \r\nOpen the installed application\t",
      "link": "http:\/\/cpactions.com\/offer\/3795\/55119",
      "icon": "http:\/\/n2.clickky.biz\/storage\/files\/c1\/u1002458\/banners\/total_domination_copy7.png",
      "payout": 6,
      "targeting": {
        "os": [
          "iPad OS"
        ],
        "countries": [
          "GB",
          "US",
          "CA",
          "AU"
        ]
      }
    }
  ]
}
JSON;


        $ParserManager = $this->container->get('parser_manager');

        $Parser = $ParserManager->getParser(ParserManager::PROVIDER_CLICKKY);

        $data = $Parser->parse($json);

        $offer = $data[0];

        $this->assertTrue($offer['external_id'] == 55120, 'Wrong offer id.');
        $this->assertTrue($offer['name'] == 'Jelly Splash iPhone/iPad', 'Wrong name.');
        $this->assertTrue($offer['payout'] == 2.1, 'Payouts not equals.');
        $this->assertTrue($offer['preview_url'] == 'http://cpactions.com/offer/3795/55120', 'Wrong preview_url got.');
        $this->assertEquals(3, count($offer['countries']), 'Wrong country count.');
        $this->assertEquals(2, count($offer['devices']), 'Wrong device count.');
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
