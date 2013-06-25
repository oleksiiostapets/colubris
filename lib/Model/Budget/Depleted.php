<?php
class Model_Budget_Depleted extends Model_Budget {
	function dsql($instance=null,$select_mode=true,$entity_code=null){
        return parent::dsql($instance,$select_mode,$entity_code)
            ->having('amount_spent>amount_eur');
    }
}
