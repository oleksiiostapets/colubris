<?php
class View_Report extends View {
    function init(){
        parent::init();

        $m=$this->add('Model_TaskTime');//->debug();
        $m->getField('user_id')->caption('Performer');
        $m->getField('spent_time')->caption('Spent');
        $m->addCondition('spent_time','>','0');
        $m->addCondition('remove_billing',false);

        $j_task = $m->join('task.id','task_id','left','_t');
        $j_task->addField('task_name','name');
        $j_task->addField('status','status');
        $j_task->addField('type','type');
        $j_task->addField('estimate','estimate');
        $j_task->addField('project_id','project_id');

        $j_project = $j_task->join('project.id','project_id','left','_p');
        $j_project->addField('project_name','name');

        $j_req = $j_task->join('requirement','requirement_id','left','_req');
        $j_req->addField('quote_id','quote_id');

        if( ($this->api->currentUser()->isDeveloper()) || $this->api->currentUser()->isClient() ){
            $mp=$this->add('Model_Project');
            if($this->api->currentUser()->isDeveloper()) $projects=$mp->forDeveloper();
            if($this->api->currentUser()->isClient()) $projects=$mp->forClient();
            $projects_ids="";
            foreach($projects->getRows() as $p){
                if($projects_ids=="") $projects_ids=$p['id'];
                else $projects_ids=$projects_ids.','.$p['id'];
            }
            $m->addCondition('project_id','in',$projects_ids);
        }

        if($this->api->recall('project_id')>0){
            $m->addCondition('project_id',$this->api->recall('project_id'));
        }
        if($this->api->recall('quote_id')>0){
            $m->addCondition('quote_id',$this->api->recall('quote_id'));
        }
        if($this->api->recall('quote_id')==-1){
            $m->addCondition('quote_id','>',0);
        }
        if($this->api->recall('quote_id')==-2){
            $m->addCondition('quote_id',null);
        }
        if($this->api->recall('performer_id')>0){
            $m->addCondition('user_id',$this->api->recall('performer_id'));
        }
        if($this->api->recall('date_from')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_from'))));
            $m->addCondition('date','>=',$date);
        }
        if($this->api->recall('date_to')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_to'))));
            $m->addCondition('date','<=',$date);
        }

        $v=$this->add('View');
        $v->setClass('right');
        $export_button=$v->add('Button')->set('Export to CSV');
        $export_button->js('click',
            $this->js()->univ()->redirect($this->api->url(null,array('action'=>'export')))
        );

        if ($_GET['action']=='export'){
            /*
            header('Content-Disposition: attachment; filename="report.csv"');
            $header=implode(";",$this->export_fields).";";

            $total_spent=0;
            $data="";
            foreach($m->getRows() as $row){
                $total_spent+=$row['spent_time'];
                foreach($this->export_fields as $field_name){
                    $data.=substr($row[$field_name],0,80).";";
                }
                $data.="\n";
            }
            $data.="TOTAL; ; ; ; ;$total_spent; ; ;";

            print "$header\n$data";
            */
            require_once '../lib/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("Oleksii Ostapets")
                ->setLastModifiedBy("Oleksii Ostapets")
                ->setTitle("Colubris report")
                ->setSubject("Colubris report")
                ->setDescription("Colubris report")
                ->setKeywords("colubris report")
                ->setCategory("Colubris report");

            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(0))->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(1))->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(2))->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(3))->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(4))->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(5))->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->getColumnIndex(6))->setWidth(15);

            for($i=0; $i<count($this->export_fields); $i++){
                $objRichText = new PHPExcel_RichText();
                $objPayable = $objRichText->createTextRun($this->export_fields[$i]);
                $objPayable->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValue($this->getColumnIndex($i).'1', $objRichText);
            }
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);

            $data=$m->getRows();
            $total_spent=0;
            for($i=0; $i<count($data); $i++){
                $objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(20);
                for($j=0; $j<count($this->export_fields); $j++){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getColumnIndex($j).($i+2),$data[$i][$this->export_fields[$j]]);
                }
                $total_spent=$total_spent+(float)$data[$i]['spent_time'];
            }

            $objRichText = new PHPExcel_RichText();
            $objPayable = $objRichText->createTextRun('TOTAL');
            $objPayable->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue($this->getColumnIndex(0).($i+2), $objRichText);

            $objRichText = new PHPExcel_RichText();
            $objPayable = $objRichText->createTextRun($total_spent);
            $objPayable->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue($this->getColumnIndex(5).($i+2), $objRichText);

            $objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(20);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="report-colubris-'.date('Y-m-i-H-i-s').'.xls"');
            header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');

            exit;
        }

        $v=$this->add('View');
        $v->setClass('cc');

        $cr=$this->add('Grid');
        $cr->addClass('zebra bordered');

        $cr->setModel($m,$this->grid_show_fields);
        $cr->addFormatter('task_name','wrap');

        $cr->addTotals(array('spent_time'));

        $cr->addColumn('expander','more');
    }

    function getColumnIndex($i){
        $columns=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        return $columns[$i];
    }
}
