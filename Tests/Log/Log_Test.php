<?php

use ABridge\ABridge\Log\Log;

class Log_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $br = "\n";
        $res = Log::reset();
        
        $this->assertTrue($res);
            
        $log= Log::get();
        
        $this->assertFalse($log->isNew());
        $this->assertTrue($log->initMeta());
        
        $log->init(['trace'=>1,'path'=>'C:/Users/pierr/ABridge/Datastore/'], []);
                        
        $log->begin();
        
        $testLine = 'test';
        $testAttributes = ['classe'=>__CLASS__];
        
        $output = 'LINE:0 classe : '.__CLASS__.$br.$testLine.$br;
        
        $this->expectOutputString($output);
        $log->logLine($testLine, $testAttributes);
        
        $this->assertEquals($testLine, $log->getLastLine());
        
        $log->end();
        
        $testLine2='not logged';
        $log->logLine($testLine2, $testAttributes);
        $this->assertEquals($testLine, $log->getLastLine());
        
        Log::reset();
        $log = Log::get();
        $this->assertNull($log->getLastLine());
        
        $log->init(['trace'=>2,'path'=>'C:/Users/pierr/ABridge/Datastore/'], []);
        
        $log->begin();
        $log->logLine($testLine, $testAttributes);
        
        $this->assertEquals($testLine, $log->getLastLine());

        $this->expectOutputString($output.$br.$output.$br);
        
        $log->end();
    }
}
