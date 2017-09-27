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
        $this->assertEquals($log->getLevel(), 0);
        $this->assertEquals($log->getRunLevel(), 0);
        
        $log->init(['trace'=>1,'path'=>'C:/Users/pierr/ABridge/Datastore/'], []);
        $this->assertEquals($log->getLevel(), 1);
        $this->assertEquals($log->getRunLevel(), 0);
        
        $log->begin();
        $this->assertEquals($log->getRunLevel(), 1);
        
        $testAttributes = [Log::TCLASS=>__CLASS__,Log::TLINE=>'10'];
        $testLine = 'test';
        $output = 'LINE:0 class : '.__CLASS__.' line : 10'.$br.$testLine.$br;

        $this->expectOutputString($output);
        $log->logLine($testLine, $testAttributes);
        
        $this->assertEquals($testLine, $log->getLastLine());
        $log->end();
        $this->assertEquals($log->getRunLevel(), 0);
        
        $testLine2='not logged';
        $log->logLine($testLine2, $testAttributes);
        $this->assertEquals($testLine, $log->getLastLine());
    }
    
    public function testLog1()
    {
        $br = "\n";
        $testAttributes = [Log::TCLASS=>__CLASS__,Log::TLINE=>'10'];
        $testLine = 'test';
        $output =$br. 'LINE:0 class : '.__CLASS__.' line : 10'.$br.$testLine.$br;

        Log::reset();
        $log = Log::get();
        
        $log->init(['trace'=>2,'path'=>'C:/Users/pierr/ABridge/Datastore/'], []);
        
        $this->assertEquals($log->getLevel(), 2);
        $this->assertEquals($log->getRunLevel(), 0);
            
        $log->begin();
        $this->assertEquals($log->getRunLevel(), 2);
        
        $log->logLine($testLine, $testAttributes);
        
        $this->assertEquals($testLine, $log->getLastLine());

        $this->expectOutputString($output.$br);
        $log->end();
    }
    
    public function testLog2()
    {
        $br = "\n";
        $testAttributes = [Log::TCLASS=>__CLASS__,Log::TLINE=>'10'];
        $testLine = 'test';
        $output = '1 lines with class : '.__CLASS__.$br;
        
        Log::reset();
        $log = Log::get();
        
        $log->init(['trace'=>2,'tdisp'=>'1','tclass'=>__CLASS__,'path'=>'C:/Users/pierr/ABridge/Datastore/'], []);
        
        $this->assertEquals($log->getLevel(), 2);
        $this->assertEquals($log->getRunLevel(), 0);
        $this->assertEquals(1, $log->getDisp());
        
        $log->begin();
        $this->assertEquals($log->getRunLevel(), 2);
        
        $log->logLine($testLine, $testAttributes);
        $this->assertEquals($testLine, $log->getLastLine());
        
        $this->expectOutputString($output);
        $log->end();
    }
    
    public function testLog3()
    {
        $br = "\n";
        $testAttributes = [Log::TCLASS=>__CLASS__,Log::TLINE=>'10'];
        $testLine = 'test';
        $output = 'LINE:0 class : '.__CLASS__.' line : 10'.$br.$testLine.$br;
        
        Log::reset();
        $log = Log::get();
        
        $log->init(['trace'=>3,'path'=>'C:/Users/pierr/ABridge/Datastore/','tclass'=>__CLASS__,'tline'=>'10',], []);
        
        
        $log->begin();
        $this->assertEquals($log->getRunLevel(), 3);
        
        $log->logLine($testLine, $testAttributes);
        
        $this->assertEquals($testLine, $log->getLastLine());
        
        $log->end();
        
        
        Log::reset();
        $log = Log::get();
        
        $log->init(['trace'=>3,'path'=>'C:/Users/pierr/ABridge/Datastore/','tclass'=>__CLASS__,'tline'=>'1',], []);
        
        
        $log->begin();
        $this->assertEquals($log->getRunLevel(), 3);
        
        $log->logLine($testLine, $testAttributes);
        
        $this->assertNull($log->getLastLine());
        
        $log->end();
    }
}
