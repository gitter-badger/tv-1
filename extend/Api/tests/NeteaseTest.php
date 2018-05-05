<?php
namespace Metowolf\Meting;
use Metowolf\Meting;
use PHPUnit\Framework\TestCase;

class NeteaseTest extends TestCase
{
    public function testSearch()
    {
        $api = new Meting('netease');
        $data = $api->format(true)->search('hello');
        $data = json_decode($data,1);
        $this->assertNotEmpty($data);
    }

    public function testSong(){
        $api = new Meting('netease');
        $data = $api->format(true)->song('35847388');
        $data = json_decode($data,1);
        $this->assertNotEmpty($data);
    }

    public function testPic(){
        $api = new Meting('netease');
        $data = $api->format(true)->pic('3388694837506899');
        $data = json_decode($data,1);
        $this->assertNotEmpty($data);
    }

    public function testUrl(){
        $api = new Meting('netease');
        $data = $api->format(true)->url('418603133');
        $data = json_decode($data,1);
        $this->assertNotEmpty($data);
    }

    public function testLyric(){
        $api = new Meting('netease');
        $data = $api->format(true)->lyric('418603133');
        $data = json_decode($data,1);
        $this->assertNotEmpty($data);
    }
}
