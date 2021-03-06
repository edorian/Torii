<?php
/**
 * This file is part of Torii
 *
 * @version $Revision$
 */

namespace Torii\Module\Feed;

use Torii\DatabaseTest;

/**
 * @version $Revision$
 */
class ModelTest extends DatabaseTest
{
    public function testGetNoUrls()
    {
        $model = new Model( $this->getDbal() );

        $this->assertEquals(
            array(),
            $model->getUrlList( "module_1" )
        );
    }

    public function testGetNoData()
    {
        $model = new Model( $this->getDbal() );

        $this->assertEquals(
            array(),
            $model->getUnread( "module_1" )
        );
    }

    /**
     * @depends testGetNoUrls
     */
    public function testAddUrl()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $this->assertEquals(
            array(
                new Struct\Url( 1, "http://example.com", "test" ),
            ),
            $model->getUrlList( "module_1" )
        );
    }

    public function testAddTwoUrls()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test1", "http://example.com/1" );
        $model->addUrl( "module_1", "test2", "http://example.com/2" );

        $this->assertEquals(
            array(
                new Struct\Url( 1, "http://example.com/1", "test1" ),
                new Struct\Url( 2, "http://example.com/2", "test2" ),
            ),
            $model->getUrlList( "module_1" )
        );
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testAddSameUrl()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );
        $model->addUrl( "module_1", "test", "http://example.com" );
    }

    /**
     * @depends testAddUrl
     */
    public function testRemoveUrl()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );
        $model->removeurl( "module_1", 1 );

        $this->assertEquals(
            array(),
            $model->getUrlList( "module_1" )
        );
    }

    public function testRemoveUnknownUrl()
    {
        $model = new Model( $this->getDbal() );
        $model->removeurl( "module_1", 42 );

        $this->assertEquals(
            array(),
            $model->getUrlList( "module_1" )
        );
    }

    /**
     * @depends testAddUrl
     */
    public function testGetPendingUrl()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $this->assertEquals(
            array(
                new Struct\Url( 1, 'http://example.com' ),
            ),
            $model->getPending( 300 )
        );
    }

    /**
     * @depends testAddUrl
     */
    public function testUpdateUrl()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $model->updateUrl( 1, 200, time() );

        $this->assertEquals(
            array(),
            $model->getPending( 300 )
        );
    }

    /**
     * @depends testAddUrl
     */
    public function testUpdateBack()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $model->updateUrl( 1, 200, time() - 400 );

        $this->assertEquals(
            array(
                new Struct\Url( 1, 'http://example.com' ),
            ),
            $model->getPending( 300 )
        );
    }

    /**
     * @depends testAddUrl
     */
    public function testGetUnread()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $this->assertEquals(
            array(),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testGetUnread
     */
    public function testAddEntry()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );
        $model->addEntry(
            1,
            'http://example.com/1',
            12345,
            'Foo'
        );

        $this->assertEquals(
            array(
                new Struct\FeedEntry( 1, 'http://example.com/1', 'test', 'Foo' ),
            ),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testAddEntry
     */
    public function testSortEntries()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $model->addEntry( 1, 'http://example.com/1', 12345, 'Foo' );
        $model->addEntry( 1, 'http://example.com/2', 12346, 'Foo' );

        $this->assertEquals(
            array(
                new Struct\FeedEntry( 2, 'http://example.com/2', 'test', 'Foo' ),
                new Struct\FeedEntry( 1, 'http://example.com/1', 'test', 'Foo' ),
            ),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testAddEntry
     */
    public function testMergeModules()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test1", "http://example.com/1" );
        $model->addUrl( "module_1", "test2", "http://example.com/2" );

        $model->addEntry( 1, 'http://example.com/1/1', 12345, 'Foo' );
        $model->addEntry( 2, 'http://example.com/2/1', 12346, 'Foo' );

        $this->assertEquals(
            array(
                new Struct\FeedEntry( 2, 'http://example.com/2/1', 'test2', 'Foo' ),
                new Struct\FeedEntry( 1, 'http://example.com/1/1', 'test1', 'Foo' ),
            ),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testAddEntry
     */
    public function testIgnoreUrlReadd()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $model->addEntry( 1, 'http://example.com/1', 12345, 'Foo' );
        $model->addEntry( 1, 'http://example.com/1', 12345, 'Foo' );

        $this->assertEquals(
            array(
                new Struct\FeedEntry( 1, 'http://example.com/1', 'test', 'Foo' ),
            ),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testAddEntry
     */
    public function testMarkRead()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test", "http://example.com" );

        $model->addEntry( 1, 'http://example.com/1', 12345, 'Foo' );
        $model->addEntry( 1, 'http://example.com/2', 12346, 'Foo' );

        $model->markRead( "module_1", 2 );

        $this->assertEquals(
            array(
                new Struct\FeedEntry( 1, 'http://example.com/1', 'test', 'Foo' ),
            ),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testAddEntry
     */
    public function testClear()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test1", "http://example.com/1" );
        $model->addUrl( "module_1", "test2", "http://example.com/2" );

        $model->addEntry( 1, 'http://example.com/1.1', 12345, 'Foo' );
        $model->addEntry( 1, 'http://example.com/1.2', 12346, 'Foo' );
        $model->addEntry( 2, 'http://example.com/2.1', 12347, 'Foo' );
        $model->addEntry( 2, 'http://example.com/2.2', 12348, 'Foo' );

        $model->clear( "module_1", 'test2' );

        $this->assertEquals(
            array(
                new Struct\FeedEntry( 2, 'http://example.com/1.2', 'test1', 'Foo' ),
                new Struct\FeedEntry( 1, 'http://example.com/1.1', 'test1', 'Foo' ),
            ),
            $model->getUnread( 'module_1' )
        );
    }

    /**
     * @depends testAddEntry
     */
    public function testGetUrlsWithoutFavicon()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test1", "http://example.com/1" );
        $model->addUrl( "module_1", "test2", "http://example.com/2" );

        $this->assertEquals(
            array(
                new Struct\Url( 1, 'http://example.com/1' ),
                new Struct\Url( 2, 'http://example.com/2' ),
            ),
            $model->getUrlsWithoutFavicon()
        );
    }

    /**
     * @depends testGetUrlsWithoutFavicon
     */
    public function testUpdateFavicon()
    {
        $model = new Model( $this->getDbal() );
        $model->addUrl( "module_1", "test1", "http://example.com/1" );
        $model->addUrl( "module_1", "test2", "http://example.com/2" );

        $model->updateFavicon( 2, 'example.ico' );

        $this->assertEquals(
            array(
                new Struct\Url( 1, 'http://example.com/1' ),
            ),
            $model->getUrlsWithoutFavicon()
        );
    }
}

