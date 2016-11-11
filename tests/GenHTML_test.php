<?php
	

require_once('GenHTML.php');


class GenHTML_Test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider Provider1
     */
 
	public function testFormElm($a,$expected)
    {
        $this->assertEquals($expected,genFormElem($a,false));
    }
 
    public function Provider1() {
        return [
            [[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_TEXT],
			 '<input type = "text" name = "A" value = "a1"  >'."\n"],
			[[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_RADIO,H_VALUES=>["a1","a2"], "separator" => "<br/>" ],
			 '<input type = "radio" name = "A"  value = "a1"  checked  ><br/>'."\n".
			 '<input type = "radio" name = "A"  value = "a2"  ><br/>'."\n"],
			[[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_SELECT,H_VALUES=>["a1","a2"], "separator" => "<br/>" ],
			 '<select name = "A"  >'."\n".
	         "\t".'<option  value = "a1"  selected  >a1</option>'."\n".
			 "\t".'<option  value = "a2"  >a2</option>'."\n".
			 '</select>'."\n"],
			[[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_SUBMIT],
			 '<input type="submit" value="Submit">'."\n"],
			 [[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_TEXTAREA],
			 '<textarea name = "A"  cols="30"  rows="10"  >a1</textarea>'."\n"],
			 [[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_TEXTAREA,H_COL=>50,H_ROW=>10],
			 '<textarea name = "A"  cols="50"  rows="10"  >a1</textarea>'."\n"],
			 [[H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_PASSWORD],
			 '<input type = "password" name = "A" value = "a1"  >'."\n"],
			 [[H_TYPE=>H_T_PLAIN,H_DEFAULT=>"this is a text string"],
			 'this is a text string'."\n"],
			 [[H_TYPE=>H_T_LINK,H_NAME=>'ABridge.php/code/1',H_LABEL=>'testSuite'],
			 '<a href=ABridge.php/code/1 >testSuite</a>'."\n"],
			];
    }

}
?>	