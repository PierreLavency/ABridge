<?php
	require_once("Handler.php"); 
	require_once("Model.php"); 
	
	class ModBase {

		private $Base;
		
		function __construct($base=0) {
			if ($base) {
				$this->Base=$base;
			}
			else {
				$this->Base=getBaseHandler('fileBase');
			}
		}
		
		public function saveMod($mod) {
			$name = $mod->getModName();
			$meta['attr_lst'] = $mod->getAllAttr();
			$meta['attr_typ'] = $mod->getAllAttrTyp();
			$meta['attr_path'] = $mod->getAllPath();
			return ($this->Base->newMod($name,$meta)); // should deal with case where it exisits already !
		}
		
		public function restoreMod($mod) {
			$name = $mod->getModName();
			$values = $this->Base->getMod($name); 
			if (!$values) {return 0;}
			$attrlist=$values['attr_lst'];
			$attrtype=$values['attr_typ'];
			$attrpath=$values['attr_path'];
			$predef = $mod->getPreDefAttr();
			foreach($attrlist as $attr) {
				if (! in_array ($attr,$predef)) {
					$typ= $attrtype[$attr];
					$path=0;
					if (array_key_exists ($attr,$attrpath)){$path=$attrpath[$attr];}
					$mod->addAttr($attr,$typ,$path);
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
			return ($this->Base->putObj($name,$id,$values)); 
		}

		public function restoreObj($mod) {
			$name = $mod->getModName();
			$id = $mod->getId();
			if ($id==0) {return 0;}
			$values = $this->Base->getObj($name, $id); 
			if (!$values) {return 0;}
			foreach($values as $attr=>$val) {
				$mod->setVal($attr,$val,false);
			};
			return $id;
		}

	}
?>
