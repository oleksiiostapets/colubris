<?php
class page_quotation extends Page {
    function page_index(){

        $v=$this->add('View')->setClass('span6 left');
        $f=$v->add('Form');

        // Client data
        $f->add('H4')->set('Your datails:');
        $f->addField('line','client_name')->setCaption('Name');
        $f->addField('line','email');

        // Project data
        $f->add('H4')->set('Project datails:');
        $f->addField('line','project_name')->setCaption('Name');
        $f->addField('text','project_description')->setCaption('Description');

        // Quote data
        $f->add('H4')->set('Quote datails:');
        $f->addField('line','quote_name')->setCaption('Name');
        $f->addField('text','quote_description')->setCaption('Description');

        $f->addSubmit('Next Step');

        if($f->isSubmitted()){
            if(trim($f->get('client_name'))==''){
                $f->getElement('client_name')->displayFieldError('Cannot be empty!')->execute();
            }

            if(trim($f->get('email'))==''){
                $f->getElement('email')->displayFieldError('Cannot be empty!')->execute();
            }
            if(!filter_var($f->get('email'),FILTER_VALIDATE_EMAIL)){
                $f->getElement('email')->displayFieldError('Wrong email format!')->execute();
            }

            $user_check=$this->add('Model_User_Base')->tryLoadBy('email',$f->get('email'));
            if ($user_check->loaded()) $f->getElement('email')->displayFieldError('This email already registered!')->execute();

            if(trim($f->get('project_name'))==''){
                $f->getElement('project_name')->displayFieldError('Cannot be empty!')->execute();
            }
            $project_check=$this->add('Model_Project_Guest')->tryLoadBy('name',$f->get('project_name'));
            if ($project_check->loaded()) $f->getElement('project_name')->displayFieldError('This project already registered!')->execute();

            if(trim($f->get('project_description'))==''){
                $f->getElement('project_description')->displayFieldError('Cannot be empty!')->execute();
            }

            if(trim($f->get('quote_name'))==''){
                $f->getElement('quote_name')->displayFieldError('Cannot be empty!')->execute();
            }

            if(trim($f->get('quote_description'))==''){
                $f->getElement('quote_description')->displayFieldError('Cannot be empty!')->execute();
            }

            $organisation=$this->add('Model_Organisation');
            $organisation->tryLoadBy('name','AgileTech');

            if ($organisation->loaded()){
                $client=$this->add('Model_Client_Guest');
                $client->set('name',$f->get('client_name'));
                $client->set('email',$f->get('email'));
                $client->set('organisation_id',$organisation->get('id'));
                $client->save();

                $user=$this->add('Model_User');
                $user->set('organisation_id',$organisation->get('id'));
                $user->set('name',$f->get('client_name'));
                $user->set('email',$f->get('email'));
                $pass=rand(1000000,999999);
                $user->set('password',$pass);
                $user->set('client_id',$client->get('id'));
                $user->save();

                $this->api->mailer->addReceiverByUserId($user->get('id'),true);
                $this->api->mailer->sendMail('user_created',array(
                    'password'=>$pass,
                ));

                $project=$this->add('Model_Project_Guest');
                $project->set('client_id',$client->get('id'));
                $project->set('name',$f->get('project_name'));
                $project->set('descr',$f->get('project_description'));
                $project->set('organisation_id',$organisation->get('id'));
                $project->save();

                $quote=$this->add('Model_Quote_Guest');
                $quote->set('project_id',$project->get('id'));
                $quote->set('name',$f->get('quote_name'));
                $quote->set('general_description',$f->get('quote_description'));
                $quote->set('quotation_requested');
                $quote->set('organisation_id',$organisation->get('id'));
                $quote->save();

                $this->api->memorize('guest_quote_id',$quote->get('id'));

                $this->js()->univ()->redirect($this->api->url('quotation2'))->execute();
            }else{
                $f->getElement('quote_description')->displayFieldError('Sorry, something wrong. Please contact with us.')->execute();
            }

        }
    }
}
