<?php
use ABridge\ABridge\CModel;

class Person extends CModel
{
	
	public function getVal($attr)
	{
		if ($attr == 'Age') {
			$a = $this->mod->getValN('BirthDay');
			if (is_null($a)) {
				return null;
			}
			$b = $this->mod->getValN('DeathDate');
			if (! is_null($b)) {
				return null;
			}
			$da= date_create($a);
			$db= date_create($b);
			$res = date_diff($da, $db);
			$res = (int) $res->format('%y');
			return $res;
		} else {
			return $this->mod->getValN($attr);
		}
	}
	
	public function save()
	{
		$a = $this->mod->getValN('BirthDay');
		$b = $this->mod->getValN('DeathDate');
		if (!is_null($b) and ! is_null($a)) {
			$da= date_create($a);
			$db= date_create($b);
			$res = date_diff($da, $db);
			$res = (int) $res->format('%y');
			$this->mod->setValN('DeathAge',$res);
		}
		return $this->mod->saveN();
	}
	
}