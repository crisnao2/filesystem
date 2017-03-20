<?php
namespace Test\Crisnao2\FileSystem;
use \Crisnao2\FileSystem\Dir;

/**
* This class performs the tests related to the directory
* Test Scan the directory
* Test Search in directory
*
* @author Cristiano Soares <crisnao2@yahoo.com.br>
* @link http://comerciobr.com
*/
class DirTest Extends BaseTestCase
{
    /**
    * This method test the scan recursive in directory
    */
    public function testScan()
    {
        $jsonRoot = '{"first_level":{"file":["file_first_level.txt"],"first_level\/second_level":{"file":["file_second_level.txt"]}}}';
        $jsonFistLevel = '{"file":["file_first_level.txt"],"second_level":{"file":["file_second_level.txt"]}}';
        $jsonSecondLevel = '{"file":["file_second_level.txt"]}';

        $filesOrDir = Dir::scan($this->dir);
        $this->assertJsonStringEqualsJsonString($jsonRoot,
            json_encode($filesOrDir), 'fails scan root');

        $filesOrDir = Dir::scan($this->dir . $this->dirFirstLevel);
        $this->assertJsonStringEqualsJsonString($jsonFistLevel,
            json_encode($filesOrDir), 'fails scan 1° level');

        $filesOrDir = Dir::scan($this->dir . $this->dirSecondLevel);
        $this->assertJsonStringEqualsJsonString($jsonSecondLevel,
            json_encode($filesOrDir), 'fails scan 2° level');
    }

    /**
    * This method test the scan recursively in directory by level of the depth
    */
    public function testScanWithDepth()
    {
        $jsonRootDepth1 = '{"first_level":false}';
        $jsonRootDepth2 = '{"first_level":{"file":["file_first_level.txt"],"first_level\/second_level":false}}';

        $jsonFistLevelDepth1 = '{"file":["file_first_level.txt"],"second_level":false}';
        $jsonFistLevelDepth2 = '{"file":["file_first_level.txt"],"second_level":{"file":["file_second_level.txt"]}}';

        $jsonSecondLevelDepth1 = '{"file":["file_second_level.txt"]}';

        $filesOrDir = Dir::scan($this->dir, 1);
        $this->assertJsonStringEqualsJsonString($jsonRootDepth1,
            json_encode($filesOrDir), 'fails scan root depth 1');

        $filesOrDir = Dir::scan($this->dir, 2);
        $this->assertJsonStringEqualsJsonString($jsonRootDepth2,
            json_encode($filesOrDir), 'fails scan root depth 2');

        $filesOrDir = Dir::scan($this->dir . $this->dirFirstLevel, 1);
        $this->assertJsonStringEqualsJsonString($jsonFistLevelDepth1,
            json_encode($filesOrDir), 'fails scan 1° level in depth 1');

        $filesOrDir = Dir::scan($this->dir . $this->dirFirstLevel, 2);
        $this->assertJsonStringEqualsJsonString($jsonFistLevelDepth2,
            json_encode($filesOrDir), 'fails scan 1° level in depth 2');

        $filesOrDir = Dir::scan($this->dir . $this->dirSecondLevel, 1);
        $this->assertJsonStringEqualsJsonString($jsonSecondLevelDepth1,
            json_encode($filesOrDir), 'fails scan 2° level in depth 1');
    }

    /**
    * This method test the search in directory
    */
    public function testSearch()
    {
        $jsonSearchNoParam = '["first_level","first_level\/file_first_level.txt","first_level\/second_level","first_level\/second_level\/file_second_level.txt"]';
        $jsonSearchFileFirst = '["first_level\/file_first_level.txt"]';
        $jsonSearchFileSecond = '["first_level\/second_level\/file_second_level.txt"]';
        $jsonSearchSecondLevel = '["file_second_level.txt"]';

        $filesOrDir = Dir::search($this->dir, '');
        $this->assertJsonStringEqualsJsonString($jsonSearchNoParam,
            json_encode($filesOrDir), 'fails search start in basedir no param');

        $filesOrDir = Dir::search($this->dir, $this->fileFirst);
        $this->assertJsonStringEqualsJsonString($jsonSearchFileFirst,
            json_encode($filesOrDir), 'fails search File First');
            
        $filesOrDir = Dir::search($this->dir, $this->fileSecond);
        $this->assertJsonStringEqualsJsonString($jsonSearchFileSecond,
            json_encode($filesOrDir), 'fails search File Second');
            
        $filesOrDir = Dir::search($this->dir . $this->dirSecondLevel, $this->fileSecond);
        $this->assertJsonStringEqualsJsonString($jsonSearchSecondLevel,
            json_encode($filesOrDir), 'fails search File Second in Second Level');
    }
}

?>