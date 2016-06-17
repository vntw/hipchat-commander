<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\Lunch;

use Venyii\HipChatCommander\Test\WebTestCase;

class PackageTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $yml = <<<YML
packages:
  - Venyii\HipChatCommander\Package\Lunch

rooms:
  - id: 7331
    packages:
      - name: lunch
        options:
          default_stores:
            kantina: Kantina
            doener: Döner
            leos: Leo´s
            metz: Metzger
            mcd: McDonalds
            bk: Burger-King
            sub: Subway
            inder: Inder
            grieche: Grieche
            cw: Curry-Wurst
YML;

        $this->createTestConfig($yml);
    }

    public function testWorkflow()
    {
        $data = $this->buildDummyData('/essen init', '1337');

        $response = $this->request($data, '/bot/addon');

        $this->assertContains('(successful) Successfully initiated a new vote!', $response->getContent());
        $data['item']['message']['message'] = '/essen vote mcd bk';
        $response = $this->request($data);
        $responseJson = json_decode($response->getContent(), true);
        //$this->assertContains('(successful) Vote added successfully!', $response->getContent());
        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td></tr><tr><td>[1]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);

        $data['item']['message']['from']['mention_name'] = 'LucaToni';
        $data['item']['message']['message'] = '/essen vote bk kantina';
        $response = $this->request($data);
        $responseJson = json_decode($response->getContent(), true);
        //$this->assertContains('(successful) Vote added successfully!', $response->getContent());
        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>LucaToni&nbsp;&nbsp;</td></tr><tr><td>[<b>✔2</b>]</td><td><b>Burger-King&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[1]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);

        $data['item']['message']['message'] = '/essen status';
        $response = $this->request($data);
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>LucaToni&nbsp;&nbsp;</td></tr><tr><td>[<b>✔2</b>]</td><td><b>Burger-King&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[1]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);

        $data['item']['message']['from']['mention_name'] = 'Rutegar';
        $data['item']['message']['message'] = '/essen vote metz kantina doener mcd';
        $response = $this->request($data);
        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>LucaToni&nbsp;&nbsp;</td><td>Rutegar&nbsp;&nbsp;</td></tr><tr><td>[2]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[2]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[2]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);

        $data['item']['message']['message'] = '/essen status';
        $response = $this->request($data);
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>LucaToni&nbsp;&nbsp;</td><td>Rutegar&nbsp;&nbsp;</td></tr><tr><td>[2]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[2]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[2]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testEmptyStatus()
    {
        $response = $this->request($this->buildDummyData('/essen status'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('Nobody voted yet!', $responseJson['message']);
    }

    public function testStatusWithAbstainsAndNoVotes()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen abstain', 32123, 'abstainUser', 'auser'));
        $this->request($this->buildDummyData('/essen abstain', 1513124, 'abstainUser2', 'auser2'));
        $response = $this->request($this->buildDummyData('/essen status'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('<table><tr><td>Abstains: auser, auser2</td></tr></table>', $responseJson['message']);
    }

    public function testStoresCmd()
    {
        $response = $this->request($this->buildDummyData('/essen stores'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(beer) Available stores: kantina (Kantina), doener (Döner), leos (Leo´s), metz (Metzger), mcd (McDonalds), bk (Burger-King), sub (Subway), inder (Inder), grieche (Grieche), cw (Curry-Wurst)', $responseJson['message']);
    }

    public function testVote()
    {
        $this->request($this->buildDummyData('/essen init'));

        $response = $this->request($this->buildDummyData('/essen vote mcd bk kantina'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td></tr><tr><td>[1]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testEmptyVoteRemovesPreviousUserVote()
    {
        $this->request($this->buildDummyData('/essen vote kantina', 98765, 'Other User', 'other_user'));
        $this->request($this->buildDummyData('/essen vote kantina'));

        $response = $this->request($this->buildDummyData('/essen vote'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>other_user&nbsp;&nbsp;</td></tr><tr><td>[<b>✔1</b>]</td><td><b>Kantina&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[0]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3">Abstains: andifined</td></tr></table>', $responseJson['message']);
    }

    public function testVotesAreSortedByVotes()
    {
        $this->request($this->buildDummyData('/essen vote kantina cw mcd', 98765, 'Other User', 'other_user'));
        $this->request($this->buildDummyData('/essen vote cw mcd', 98766, 'Other User2', 'other_user2'));

        $response = $this->request($this->buildDummyData('/essen vote mcd'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>other_user&nbsp;&nbsp;</td><td>other_user2&nbsp;&nbsp;</td><td>andifined&nbsp;&nbsp;</td></tr><tr><td>[<b>✔3</b>]</td><td><b>McDonalds&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[2]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[1]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testVoteStoreAutoCorrect()
    {
        $response = $this->request($this->buildDummyData('/essen vote ind0r subb'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td></tr><tr><td>[1]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testInvalidVoteReturnsError()
    {
        $this->request($this->buildDummyData('/essen init'));

        $response = $this->request($this->buildDummyData('/essen vote mcd bk unknown'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(failed) Invalid store: `unknown` - Check the available stores using the `stores` command!', $responseJson['message']);
    }

    public function testAddStore()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote mcd cw inder', null, null, 'Rutegar'));

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>Rutegar&nbsp;&nbsp;</td></tr><tr><td>[<b>✔2</b>]</td><td><b>McDonalds&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[1]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[1]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen mkstore kol Kolibri-Tal'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('(successful) Successfully added the store!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen mkstore sws Store With Space'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('(successful) Successfully added the store!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertStringEndsWith(', sws (Store With Space)', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>Rutegar&nbsp;&nbsp;</td></tr><tr><td>[<b>✔2</b>]</td><td><b>McDonalds&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[1]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[1]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kolibri-Tal&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Store With Space&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testAddStoreWithExistingVotes()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $response = $this->request($this->buildDummyData('/essen mkstore kol Kolibri-Tal'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(successful) Successfully added the store!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertStringEndsWith(', kol (Kolibri-Tal)', $responseJson['message']);
    }

    public function testInvalidAddStore()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $response = $this->request($this->buildDummyData('/essen mkstore kol K'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(failed) The store names must be at least 2 characters!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen mkstore wokm'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(doh) Usage: mkstore name_short name_long', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertStringEndsNotWith(', kol (Kolibri-Tal)', $responseJson['message']);
    }

    public function testRemoveStore()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen mkstore kol Kolibri-Tal'));
        $this->request($this->buildDummyData('/essen mkstore drg Dragons'));

        $response = $this->request($this->buildDummyData('/essen rmstore kol'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(successful) Successfully removed the store!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertNotContains('Kolibri-Tal', $responseJson['message']);
    }

    public function testInvalidRemoveStore()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen mkstore kol Kolibri-Tal'));
        $this->request($this->buildDummyData('/essen mkstore drg Dragons'));

        $response = $this->request($this->buildDummyData('/essen rmstore kol'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(successful) Successfully removed the store!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertNotContains('Kolibri-Tal', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen rmstore mcd'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(failed) This is a default store... so... no. (okay)', $responseJson['message']);
    }

    public function testGoCmd()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote inder bk', 34522, 'us_er', 'us_er'));

        $response = $this->request($this->buildDummyData('/essen go bk'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('Time To Eat @ Burger-King! @andifined @us_er', $responseJson['message']);
    }

    public function testEmptyGoCmd()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote inder bk', 34522, 'us_er', 'us_er'));

        $response = $this->request($this->buildDummyData('/essen go cw'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('Nobody wants to go there!', $responseJson['message']);
    }

    public function testGoCmdUsesBestStoreIfStoreIsNotPassed()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote inder bk', 34522, 'us_er', 'us_er'));

        $response = $this->request($this->buildDummyData('/essen go'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('Time To Eat @ Burger-King! @andifined @us_er', $responseJson['message']);

        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote inder cw', 34522, 'us_er', 'us_er'));

        $response = $this->request($this->buildDummyData('/essen go'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertStringStartsWith('Time To Eat @ ', $responseJson['message']);
    }

    public function testGoCmdScreamsIfNoVotesAvailable()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $response = $this->request($this->buildDummyData('/essen go'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertRegExp('/Nobody voted for anything yet!/', $responseJson['message']);
    }

    public function testInitCmd()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen mkstore drg Dragons'));
        $this->request($this->buildDummyData('/essen abstain', 32123, 'abstainUser', 'auser'));

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertNotContains('Nobody voted yet!', $responseJson['message']);
        $this->assertContains('Abstains: auser', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertStringEndsWith(', drg (Dragons)', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen init'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('(successful) Successfully initiated a new vote!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('Nobody voted yet!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->assertNotContains('Abstasins:', $response->getContent());

        $response = $this->request($this->buildDummyData('/essen stores'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertStringEndsWith(', drg (Dragons)', $responseJson['message']);
    }

    public function testClearCmd()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen mkstore drg Dragons'));
        $this->request($this->buildDummyData('/essen abstain', 32123, 'abstainUser', 'auser'));

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertNotContains('Nobody voted yet!', $responseJson['message']);
        $this->assertContains('Abstains: auser', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen stores'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertStringEndsWith(', drg (Dragons)', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen clear'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('(successful) Successfully cleared all votes and custom stores!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('Nobody voted yet!', $responseJson['message']);

        $response = $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->assertNotContains('Abstasins:', $response->getContent());

        $response = $this->request($this->buildDummyData('/essen stores'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertNotContains('drg (Dragons)', $responseJson['message']);
    }

    public function testDecideCmd()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote mcd bk', 324324, 'rutegar', 'rutegar'));
        $this->request($this->buildDummyData('/essen vote cw', 231321, 'nother', 'nother'));

        $response = $this->request($this->buildDummyData('/essen decide'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertStringStartsWith('Time To Eat @ ', $responseJson['message']);
        $this->assertStringEndsWith('! @andifined @rutegar', $responseJson['message']);
    }

    public function testDecideCmdWithNoVotes()
    {
        $this->request($this->buildDummyData('/essen clear'));

        $response = $this->request($this->buildDummyData('/essen decide'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('(failed) What exactly do you want me to decide? Nobody even voted! (facepalm)', $responseJson['message']);
    }

    public function testAbstainCmd()
    {
        $this->request($this->buildDummyData('/essen clear'));
        $this->request($this->buildDummyData('/essen vote mcd bk'));
        $this->request($this->buildDummyData('/essen vote mcd bk', 324324, 'rutegar', 'rutegar'));
        $this->request($this->buildDummyData('/essen vote cw', 231321, 'nother', 'nother'));
        $this->request($this->buildDummyData('/essen vote doener cw mcd', 89744, 'not_hungry', 'not_hungry'));

        $response = $this->request($this->buildDummyData('/essen abstain', 89744, 'not_hungry', 'not_hungry'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>rutegar&nbsp;&nbsp;</td><td>nother&nbsp;&nbsp;</td></tr><tr><td>[2]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[2]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[1]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td></tr><tr><td colspan="5">&nbsp;</td></tr><tr><td colspan="5">Abstains: not_hungry</td></tr></table>', $responseJson['message']);

        $this->request($this->buildDummyData('/essen vote doener cw mcd', 89744, 'not_hungry', 'not_hungry'));

        $response = $this->request($this->buildDummyData('/essen status'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $responseJson = json_decode($response->getContent(), true);
        $this->assertSame('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>andifined&nbsp;&nbsp;</td><td>rutegar&nbsp;&nbsp;</td><td>nother&nbsp;&nbsp;</td><td>not_hungry&nbsp;&nbsp;</td></tr><tr><td>[<b>✔3</b>]</td><td><b>McDonalds&nbsp;&nbsp;</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td><td><b>&nbsp;&nbsp;&nbsp;-</b></td><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</b></td></tr><tr><td>[2]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[2]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testAckCmd()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote mcd bk kantina', 31337, 'leader', 'leader'));

        $response = $this->request($this->buildDummyData('/essen ack leader', 31338, 'follower', 'follower'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>leader&nbsp;&nbsp;</td><td>follower&nbsp;&nbsp;</td></tr><tr><td>[2]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[2]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[2]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[0]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testAckCmdWithoutMentionAppliesLastVote()
    {
        $this->request($this->buildDummyData('/essen init'));
        $this->request($this->buildDummyData('/essen vote sub cw', 31337, 'leader', 'leader'));
        $this->request($this->buildDummyData('/essen vote mcd bk kantina', 31337, 'homer', 'homerJ'));

        $response = $this->request($this->buildDummyData('/essen ack', 31338, 'follower', 'follower'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>leader&nbsp;&nbsp;</td><td>homerJ&nbsp;&nbsp;</td><td>follower&nbsp;&nbsp;</td></tr><tr><td>[2]</td><td>Burger-King&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[2]</td><td>Kantina&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[2]</td><td>McDonalds&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✓</td></tr><tr><td>[1]</td><td>Curry-Wurst&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[1]</td><td>Subway&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;✓</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Döner&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Grieche&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Inder&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Leo´s&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr><tr><td>[0]</td><td>Metzger&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;-</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-</td></tr></table>', $responseJson['message']);
    }

    public function testAckCmdWithoutMentionButWithoutVotesReturnsError()
    {
        $this->request($this->buildDummyData('/essen init'));

        $response = $this->request($this->buildDummyData('/essen ack', 31338, 'follower', 'follower'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(failed) Nobody voted yet, looks like you`ll have to choose on your own this time!', $responseJson['message']);
    }

    public function testAckWithoutVotes()
    {
        $this->request($this->buildDummyData('/essen init'));

        $response = $this->request($this->buildDummyData('/essen ack leader', 31338, 'follower', 'follower'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('(failed) Could not find any vote for user `leader`', $responseJson['message']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageName()
    {
        return 'lunch';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\Lunch';
    }
}
