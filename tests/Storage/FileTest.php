<?php

namespace SimpleLog\Test;

use SimpleLog\Storage\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * store generated log file path
     *
     * @var string
     */
    protected $logPath;

    /**
     * @var array
     */
    protected $fileConfig = [];

    /**
     * @var string
     */
    protected $testLog = 'notice';

    /**
     * @var string
     */
    protected $fullTestFilePath;

    /**
     * @var array
     */
    protected $testMessage = [
        'Some log message',
        'Some another log message',
    ];

    /**
     * actions launched before test starts
     */
    protected function setUp()
    {
        $this->logPath = dirname(__FILE__) . '/../log';
        $this->fileConfig = ['log_path' => $this->logPath];
        $this->fullTestFilePath = $this->logPath . '/' . $this->testLog . '.log';

        $this->tearDown();
    }

    public function testCreateLogFile()
    {
        $this->assertFileNotExists($this->fullTestFilePath);

        (new File($this->fileConfig))->store($this->testMessage[0], $this->testLog);

        $this->assertFileExists($this->fullTestFilePath);

        $content = file_get_contents($this->fullTestFilePath);
        $this->assertEquals($this->testMessage[0], $content);
    }

    public function testAddMessageForExistingLog()
    {
        $storage = new File($this->fileConfig);

        $this->assertFileNotExists($this->fullTestFilePath);

        $storage->store($this->testMessage[0], $this->testLog);

        $this->assertFileExists($this->fullTestFilePath);

        $content = file_get_contents($this->fullTestFilePath);
        $this->assertEquals($this->testMessage[0], $content);

        $storage->store($this->testMessage[1], $this->testLog);

        $this->assertFileExists($this->fullTestFilePath);

        $content = file_get_contents($this->fullTestFilePath);
        $this->assertEquals($this->testMessage[0] . $this->testMessage[1], $content);
    }

    /**
     * @expectedException \SimpleLog\LogException
     * @expectedExceptionMessage Unable to create log directory: /none/exists
     */
    public function testExceptionDuringCreateLogDirectory()
    {
        $storage = new File(['log_path' => '/none/exists']);

        $storage->store($this->testMessage[0], $this->testLog);
    }

    /**
     * @expectedException \SimpleLog\LogException
     */
    public function testExceptionDuringSaveLogFile()
    {
        (new File(['log_path' => dirname(__FILE__) . '/../no_permission']))
            ->store($this->testMessage[0], $this->testLog);
    }

    /**
     * actions launched after test was finished
     */
    protected function tearDown()
    {
        if (file_exists($this->fullTestFilePath)) {
            unlink($this->fullTestFilePath);
        }
    }
}
