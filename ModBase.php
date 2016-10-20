<?php
	require_once("Handler.php"); 
	require_once("Model.php"); 
	
	class ModBase {

		private $Base;
		
		function __construct() {
			$this->Base=getHandler('file_persist');
			
		}
		
		public function saveMod($mod) {
			$name = $mod->getModName();
			$meta['attr_lst'] = $mod->getAllAttr();
			$meta['typ_lst'] = $mod->getAllAttrTyp();
			return ($this->Base->newMod($name,$meta));
		}
		
		public function restoreMod($mod) {
			$name = $mod->getModName();
			$values = $this->Base->getMod($name); 
			if (!$values) {return 0;}
			$attrlist=$values['attr_lst'];
			$attrtype=$values['typ_lst'];
			$predef = $mod->getPreDefAttr();
			foreach($attrlist as $attr) {
				if (! in_array ($attr,$predef)) {
					$typ= $attrtype[$attr];
					$mod->addAttr($attr,$typ,false);			
				}
			}
			return true; 	
		}

		public function saveObj($mod) {
			$name = $mod->getModName();
			$values =$mod->getAllVal();
			$id = $mod->getId();
			if ($id == 0) {
				return ($this->Base->newObj($name,$values)); 				
			}
			return ($this->Base->putObj($name,$values)); 
		}

		public function restoreObj($mod) {
			$name = $mod->getModName();
			$id = $mod->getId();
			$values = $this->Base->getObj($name, $id); 
			if (!$values) {return 0;}
			foreach($values as $attr=>$val) {
				$mod->setVal($attr,$val,false);
			}
		}

	}
?>
