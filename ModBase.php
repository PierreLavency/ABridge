<?php
	require_once("Handler.php"); 
	require_once("Model.php"); 
	
	class ModBase {

		private $Base;
		
		function __construct($base) {
			$this->Base=$base;
		}
		
		public function getBase() {
			return $this->Base;
		}
		
		public function eraseMod($mod) {
			$name = $mod->getModName();
			return ($this->Base->delMod($name));

		}
		
		public function saveMod($mod) {
			$name = $mod->getModName();
			$meta['attr_lst'] = $mod->getAllAttr();
			$meta['attr_typ'] = $mod->getAllTyp();
			$meta['attr_path'] = $mod->getAllPath();
			$meta['attr_bkey'] = $mod->getAllBkey();
			$meta['attr_mdtr'] = $mod->getAllMdtr();
			if($this->Base->existsMod($name)) {return ($this->Base->putMod($name,$meta));}
			return ($this->Base->newMod($name,$meta)); // should deal with case where it exisits already !
		}
		
		public function restoreMod($mod) {
			$name = $mod->getModName();
			$values = $this->Base->getMod($name); 
			if (!$values) {return false;}
			$attrlist=$values['attr_lst'];
			$attrtype=$values['attr_typ'];
			$attrpath=$values['attr_path'];
			$attrbkey=$values['attr_bkey'];
			$attrmdtr=$values['attr_mdtr'];
			$predef = $mod->getAllPredef();
			foreach($attrlist as $attr) {
				if (! in_array ($attr,$predef)) {
					$typ= $attrtype[$attr];
					$path=0;
					if (array_key_exists ($attr,$attrpath)){$path=$attrpath[$attr];}
					$mod->addAttr($attr,$typ,$path);
					if (in_array($attr,$attrbkey)) {$mod->setBkey($attr,true);}
					if (in_array($attr,$attrmdtr)) {$mod->setMdtr($attr,true);}
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
				$typ=$mod->getTyp($attr);
				$valn=convertString($val,$typ);
				$mod->setVal($attr,$valn,false);
			};
			return $id;
		}

		public function eraseObj($mod) {
			$name = $mod->getModName();
			$id = $mod->getId();
			if ($id==0) {return true;}
			return ($this->Base->delObj($name, $id));
		}
		
		
		public function findObj($modN,$attr,$val) {
			return ($this->Base->findObj($modN,$attr,$val));
		}
		
		
		
	}
?>
