<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * @see Zend_Feed_Builder
 */
require_once 'Zend/Feed/Builder.php';

/**
 * @see Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_ImportTest extends PHPUnit_Framework_TestCase
{
    protected $_client;
    protected $_feedDir;

    /**
     * HTTP client test adapter
     *
     * @var Zend_Http_Client_Adapter_Test
     */
    protected $_adapter;

    public function setUp()
    {
        $this->_adapter = new Zend_Http_Client_Adapter_Test();
        Zend_Feed::setHttpClient(new Zend_Http_Client(null, ['adapter' => $this->_adapter]));
        $this->_client = Zend_Feed::getHttpClient();
        $this->_feedDir = dirname(__FILE__) . '/_files';
    }

    /**
     * Test an atom feed generated by google's Blogger platform
     */
    public function testAtomGoogle()
    {
        $this->_importAtomValid('AtomTestGoogle.xml');
    }

    /**
     * Test an atom feed generated by mozillaZine.org
     */
    public function testAtomMozillazine()
    {
        $this->_importAtomValid('AtomTestMozillazine.xml');
    }

    /**
     * Test an atom feed generated by O'Reilly
     */
    public function testAtomOReilly()
    {
        $this->_importAtomValid('AtomTestOReilly.xml');
    }

    /**
     * Test an atom feed generated by PlanetPHP
     */
    public function testAtomPlanetPHP()
    {
        $this->_importAtomValid('AtomTestPlanetPHP.xml');
    }

    /**
     * Test a small atom feed
     */
    public function testAtomSample1()
    {
        $this->_importAtomValid('AtomTestSample1.xml');
    }

    /**
     * Test a small atom feed without any entries
     */
    public function testAtomSample2()
    {
        $this->_importAtomValid('AtomTestSample2.xml');
    }

    /**
     * Test an atom feed with a </entry> tag missing
     */
    public function testAtomSample3()
    {
        $this->_importInvalid('AtomTestSample3.xml');
    }

    /**
     * Test an atom feed with links within entries
     */
    public function testAtomSample4()
    {
        $this->_importAtomValid('AtomTestSample4.xml');
    }

    /**
     * Test a RSS feed generated by UserLand Frontier v9.5
     */
    public function testRssHarvardLaw()
    {
        $this->_importRssValid('RssTestHarvardLaw.xml');
    }

    /**
     * Test a RSS feed generated by PlanetPHP
     */
    public function testRssPlanetPHP()
    {
        $this->_importRssValid('RssTestPlanetPHP.xml');
    }

    /**
     * Test a RSS feed generated by Slashdot
     */
    public function testRssSlashdot()
    {
        $this->_importRssValid('RssTestSlashdot.xml');
    }

    /**
     * Test a RSS feed generated by CNN
     */
    public function testRssCNN()
    {
        $this->_importRssValid('RssTestCNN.xml');
    }

    /**
     * Test a valid RSS 0.91 sample
     */
    public function testRss091Sample1()
    {
        $this->_importRssValid('RssTest091Sample1.xml');
    }

    /**
     * Test a valid RSS 0.91 sample
     */
    public function testRss092Sample1()
    {
        $this->_importRssValid('RssTest092Sample1.xml');
    }

    /**
     * Test a valid RSS 1.0 sample
     */
    public function testRss100Sample1()
    {
        $feed = $this->_importRssValid('RssTest100Sample1.xml');
        $this->assertEquals(2, $feed->count());
    }

    /**
     * Test a valid RSS 1.0 sample with some extensions in it
     */
    public function testRss100Sample2()
    {
        $feed = $this->_importRssValid('RssTest100Sample2.xml');
        $this->assertEquals(1, $feed->count());
    }

    /**
     * Test a valid RSS 2.0 sample
     */
    public function testRss200Sample1()
    {
        $this->_importRssValid('RssTest200Sample1.xml');
    }

    /**
     * Test the import of a RSS feed from an array
     */
    public function testRssImportFullArray()
    {
        $feed = Zend_Feed::importArray($this->_getFullArray(), 'rss');
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
    }

    /**
     * Test the import of a RSS feed from an array
     * @group ZF-5833
     */
    public function testRssImportSetsIsPermaLinkAsFalseIfGuidNotAUri()
    {
        $feed = Zend_Feed::importArray($this->_getFullArray(), 'rss');
        $entry = $feed->current();
        $this->assertEquals('false', $entry->guid['isPermaLink']);
    }

    /**
     * Test the import of a RSS feed from an array
     */
    public function testAtomImportFullArray()
    {
        $feed = Zend_Feed::importArray($this->_getFullArray(), 'atom');
    }

    /**
     * Test the import of a RSS feed from a builder
     */
    public function testRssImportFullBuilder()
    {
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($this->_getFullArray()), 'rss');
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
    }

    /**
     * Test the import of a full iTunes RSS feed from a builder
     */
    public function testRssImportFulliTunesBuilder()
    {
        $array = $this->_getFullArray();
        $array['itunes']['author'] = 'iTunes Author';
        $array['itunes']['owner'] = ['name' => 'iTunes Owner',
                                          'email' => 'itunes@example.com'];
        $array['itunes']['image'] = 'http://www.example/itunes.png';
        $array['itunes']['subtitle'] = 'iTunes subtitle';
        $array['itunes']['summary'] = 'iTunes summary';
        $array['itunes']['explicit'] = 'clean';
        $array['itunes']['block'] = 'no';
        $array['itunes']['new-feed-url'] = 'http://www.example/itunes.xml';
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($array), 'rss');
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
    }

    /**
     * Test the import of an Atom feed from a builder
     */
    public function testAtomImportFullBuilder()
    {
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($this->_getFullArray()), 'atom');

    }

    /**
     * Test the import of an Atom feed from a builder
     */
    public function testAtomImportFullBuilderValid()
    {
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($this->_getFullArray()), 'atom');

        $feed = Zend_Feed::importString($feed->saveXml());
        $this->assertTrue($feed instanceof Zend_Feed_Atom);
    }

    /**
     * Check the validity of the builder import (rss)
     */
    public function testRssImportFullBuilderValid()
    {
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($this->_getFullArray()), 'rss');
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
        $feed = Zend_Feed::importString($feed->saveXml());
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
    }

    /**
     * Test the return of a link() call (atom)
     */
    public function testAtomGetLink()
    {
        $feed = Zend_Feed::importBuilder(new Zend_Feed_Builder($this->_getFullArray()), 'atom');
        $this->assertTrue($feed instanceof Zend_Feed_Atom);
        $feed = Zend_Feed::importString($feed->saveXml());
        $this->assertTrue($feed instanceof Zend_Feed_Atom);
        $href = $feed->link('self');
        $this->assertEquals('http://www.example.com', $href);
    }

    /**
     * Imports an invalid feed and ensure everything works as expected
     * even if XDebug is running (ZF-2590).
     */
    public function testImportInvalidIsXdebugAware()
    {
        if (!function_exists('xdebug_is_enabled')) {
            $this->markTestIncomplete('XDebug not installed');
        }

        $response = new Zend_Http_Response(200, [], '');
        $this->_adapter->setResponse($response);

        try {
            $feed = Zend_Feed::import('http://localhost');
            $this->fail('Expected Zend_Feed_Exception not thrown');
        } catch (Zend_Feed_Exception $e) {
            $this->assertTrue($e instanceof Zend_Feed_Exception);
            $this->assertRegExp('/(XDebug is running|Empty string)/', $e->getMessage());
        }
    }

    /**
     * Returns the array used by Zend_Feed::importArray
     * and Zend_Feed::importBuilder tests
     *
     * @return array
     */
    protected function _getFullArray()
    {
        $array = ['title' => 'Title of the feed',
                       'link' => 'http://www.example.com',
                       'description' => 'Description of the feed',
                       'author' => 'Olivier Sirven',
                       'email' => 'olivier@elma.fr',
                       'webmaster' => 'olivier@elma.fr',
                       'charset' => 'iso-8859-15',
                       'lastUpdate' => time(),
                       'published' => strtotime('2007-02-27'),
                       'copyright' => 'Common Creative',
                       'image' => 'http://www.example/images/icon.png',
                       'language' => 'en',
                       'ttl' => 60,
                       'rating' => ' (PICS-1.1 "http://www.gcf.org/v2.5" labels
  on "1994.11.05T08:15-0500"
  exp "1995.12.31T23:59-0000"
  for "http://www.greatdocs.com/foo.html"
  by "George Sanderson, Jr."
  ratings (suds 0.5 density 0 color/hue 1))',
                       'cloud' => ['domain' => 'rpc.sys.com',
                                        'path' => '/rpc',
                                        'registerProcedure' => 'webServices.pingMe',
                                        'protocol' => 'xml-rpc'],
                       'textInput' => ['title' => 'subscribe',
                                            'description' => 'enter your email address to subscribe by mail',
                                            'name' => 'email',
                                            'link' => 'http://www.example.com/subscribe'],
                       'skipHours' => [1, 13, 17],
                       'skipDays' => ['Saturday', 'Sunday'],
                       'itunes'  => ['block' => 'no',
                                          'keywords' => 'example,itunes,podcast',
                                          'category' => [['main' => 'Technology',
                                                                    'sub' => 'Gadgets'],
                                                              ['main' => 'Music']]],
                       'entries' => [['guid' => time(),
                                                'title' => 'First article',
                                                'link' => 'http://www.example.com',
                                                'description' => 'First article description',
                                                'content' => 'First article <strong>content</strong>',
                                                'lastUpdate' => time(),
                                                'comments' => 'http://www.example.com/#comments',
                                                'commentRss' => 'http://www.example.com/comments.xml',
                                                'source' => ['title' => 'Original title',
                                                                  'url' => 'http://www.domain.com'],
                                                'category' => [['term' => 'test category',
                                                                          'scheme' => 'http://www.example.com/scheme'],
                                                                    ['term' => 'another category']
                                                                    ],
                                                'enclosure' => [['url' => 'http://www.example.com/podcast.mp3',
                                                                           'type' => 'audio/mpeg',
                                                                           'length' => '12216320'
                                                                           ],
                                                                     ['url' => 'http://www.example.com/podcast2.mp3',
                                                                           'type' => 'audio/mpeg',
                                                                           'length' => '1221632'
                                                                           ]
                                                                     ]
                                                ],
                                          ['title' => 'Second article',
                                                'link' => 'http://www.example.com/two',
                                                'description' => 'Second article description',
                                                'content' => 'Second article <strong>content</strong>',
                                                'lastUpdate' => time(),
                                                'comments' => 'http://www.example.com/two/#comments',
                                                'category' => [['term' => 'test category']],
                                                ]
                                          ]
                       ];
        return $array;
    }

    /**
     * Import an invalid atom feed
     */
    protected function _importAtomValid($filename)
    {
        $response = new Zend_Http_Response(200, [], file_get_contents("$this->_feedDir/$filename"));
        $this->_adapter->setResponse($response);

        $feed = Zend_Feed::import('http://localhost');
        $this->assertTrue($feed instanceof Zend_Feed_Atom);
    }

    /**
     * Import a valid rss feed
     */
    protected function _importRssValid($filename)
    {
        $response = new Zend_Http_Response(200, [], file_get_contents("$this->_feedDir/$filename"));
        $this->_adapter->setResponse($response);

        $feed = Zend_Feed::import('http://localhost');
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
        return $feed;
    }

    /**
     * Imports an invalid feed
     */
    protected function _importInvalid($filename)
    {
        $response = new Zend_Http_Response(200, [], file_get_contents("$this->_feedDir/$filename"));
        $this->_adapter->setResponse($response);

        try {
            $feed = Zend_Feed::import('http://localhost');
            $this->fail('Expected Zend_Feed_Exception not thrown');
        } catch (Zend_Feed_Exception $e) {
            $this->assertTrue($e instanceof Zend_Feed_Exception);
        }
    }

    /**
     * @group ZF-5903
     */
    public function testFindFeedsIncludesUriAsArrayKey()
    {
        if (!defined('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_FEED_READER_ONLINE_ENABLED')
        ) {
            $this->markTestSkipped('testFindFeedsIncludesUriAsArrayKey() requires a network connection');
            return;
        }
        Zend_Feed::setHttpClient(new Zend_Http_Client);
        $feeds = Zend_Feed::findFeeds('http://www.planet-php.net');
        $this->assertEquals([
            'http://www.planet-php.org:80/rss/', 'http://www.planet-php.org:80/rdf/'
        ], array_keys($feeds));
    }
}
