<?php
class page_quotation extends Page {
    function page_index(){

        $this->add('H4')->set('Welcome to Colubris');
        $this->add('View')->setHtml('Colubris is an online web project management tool that allows clients (you) to submit requests for quotation, and developers (us) to estimate and manage quotes, <br />divide them in to requirements and tasks, track actual performance, and prepare reports back to clients (you). <br />Colubris is envisioned, developed and constantly upgraded by Agile55. We in Agile 55 use Colubris for communication with our clients and for management of all our development projects. <br />If you want Agile 55 to develop a website or an app for you please fill the Colubris registration form below. <br />We need your contact information and short description about a project you would like us to develop. <br />Within 2 working days we will prepare a price estimate and a list of specifying questions to you. You will get a notification about that on your indicated email address.<br /><br />Thank you!<br /><br /><strong>If you want <a href="http://agile55.com/">Agile 55</a> to develop a website or an app for you please fill the Colubris registration form below.</strong>');

/*        $this->add('View')->set('Colubris allows to manage quotes, requirements and tasks. It is being developed and supported by Agile55.');
        $this->add('View')->set('If you want to be registered to work with Agile55 please fill the form below.');
        $this->add('View')->setHtml('We need your details (contact information on left side of the form) and short information about your project (right side).');
*/
        $v=$this->add('View');
        $f=$v->add('Form');

        // Client data
        $f->add('H4')->set('Your details:');
        $f->addField('line','client_name')->setCaption('Name');
        $f->addField('line','email');
        $f->addField('line','phone');
        $f->addField('Line','captcha')->add('x_captcha/Controller_Captcha');

        // Project data
        $f->add('H4')->set('Project details:');
        $f->addField('line','project_name')->setCaption('Name');
        $f->addField('text','project_description')->setCaption('Description');

        //$f->addClass('atk-row');
        $f->add('Order')
            ->move($f->addSeparator  ('span6'),'first')
            ->move($f->addSeparator('span6'),'after','captcha')
            ->now();

        $f->addSubmit('Next Step');

        if($f->isSubmitted()){
            if (!$f->getElement('captcha')->captcha->isSame($f->get('captcha'))) {
                $f->js()->atk4_form('fieldError','captcha','Wrong captcha!')->execute();
            }
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

            $organisation=$this->add('Model_Organisation');
            $organisation->tryLoadBy('name','AgileTech');

            if ($organisation->loaded()){
                $client=$this->add('Model_Client_Guest');
                $client->set('name',$f->get('client_name'));
                $client->set('email',$f->get('email'));
                $client->set('phone',$f->get('phone'));
                $client->set('organisation_id',$organisation->get('id'));
                $client->save();

                $user=$this->add('Model_User');
                $user->set('organisation_id',$organisation->get('id'));
                $user->set('name',$f->get('client_name'));
                $user->set('email',$f->get('email'));
                $pass=rand(1000000,9999999);
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
                $quote->set('name',$f->get('project_name'));
                $quote->set('general_description',$f->get('project_description'));
                $quote->set('quotation_requested');
                $quote->set('organisation_id',$organisation->get('id'));
                $quote->set('user_id',$user->get('id'));
                $quote->set('currency','GBP');
                $quote->save();

                $this->api->memorize('guest_quote_id',$quote->get('id'));

                $this->js()->univ()->redirect($this->api->url('quotation2'))->execute();
            }else{
                $f->getElement('quote_description')->displayFieldError('Sorry, something wrong. Please contact with us.')->execute();
            }

        }
    }
}
