<?php namespace Phprest\HttpFoundation;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testSetContent()
    {
        $response = new Response('first');
        $response->setContent('second');

        $this->assertEquals('second', $response->getContent());
    }
}
