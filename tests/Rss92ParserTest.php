<?php

require_once 'lib/PicoFeed/PicoFeed.php';

use PicoFeed\Parser\Rss92;

class Rss92ParserTest extends PHPUnit_Framework_TestCase
{
    public function testFormatOk()
    {
        $parser = new Rss92(file_get_contents('tests/fixtures/univers_freebox.xml'));
        $feed = $parser->execute();

        $this->assertNotFalse($feed);
        $this->assertNotEmpty($feed->items);

        $this->assertEquals('Univers Freebox', $feed->getTitle());
        $this->assertEquals('http://www.universfreebox.com', $feed->getUrl());
        $this->assertEquals('http://www.universfreebox.com', $feed->getId());
        $this->assertEquals(time(), $feed->date);
        $this->assertEquals(30, count($feed->items));

        $this->assertEquals('Retour de Xavier Niel sur Twitter, « sans initiative privée, pas de révolution #Born2code »', $feed->items[0]->title);
        $this->assertEquals('http://www.universfreebox.com/article20302.html', $feed->items[0]->getUrl());
        $this->assertEquals('ad23a45af194cc46d5151a9a062c5841b03405e456595c30b742d827e08af2e0', $feed->items[0]->getId());
        $this->assertEquals('', $feed->items[0]->getAuthor());
    }
}