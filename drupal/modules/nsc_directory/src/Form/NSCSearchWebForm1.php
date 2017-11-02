<?php


namespace Drupal\nsc_directory\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class NSCSearchWebForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'submission_North_Seattle_Colleges_directory_lookup';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#prefix'] = '<div id="errMsg"><span id="err_msg_text"></span></div><h1>Search North Seattle Colleges Directory</h1>';
        $form['last_name'] = array(
            '#id' => 'form_last_name',
            '#type' => 'textfield',
            '#required' => FALSE,
            '#title' => t('Last Name'),
            '#maxlength' => 25,
            '#prefix' => '<div class="form-group form-inline"><div class="row">',
            '#suffix' => '</div></div>',
            '#description' => t('Last name of the person you are searching'),

        );
        $form['first_name'] = array(
            '#id' => 'form_first_name',
            '#type' => 'textfield',
            '#required' => FALSE,
            '#description' => 'First name of the person you are searching',
            '#maxlength' => 25,
            '#prefix' => '<div class="form-group form-inline"><div class="row">',
            '#suffix' => '</div></div>',
            '#title' => t('First Name'),

        );
        $form['email_mail'] = array(
            '#id' => 'form_email_mail',
            '#type' => 'email',
            '#required' => FALSE,
            '#description' => 'Email of the person you are searching',
            '#maxlength' => 45,
            '#prefix' => '<div class="form-group form-inline"><div class="row">',
            '#suffix' => '</div></div>',
            '#title' => t('Email'),

        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#id' => 'form_submit_main',
            '#title' => t("Fill out atleast one of the fields to start searching"),
            '#attributes' => array('class' => array('form-control', 'col-12'), 'disabled' => 'disabled'),
            '#value' => t('Search'),
            '#button_type' => 'primary',
        );

        if ($form_state->getValue('result')) {
            $form['result'] = array('#type' => 'markup', '#markup' => $form_state->getValue('result'), '#attributes' => array('class' => array('form-control')), '#prefix' => '<div class="container-inline form-group">',
                '#suffix' => '</div>');
        }

        $form['#suffix'] = '<h2>District Wide Faculty and Staff</h2> If you are trying to contact a particular member of staff or faculty please use our <a href="http://www.seattlecolleges.com/DISTRICT/employeedirectory/directorysearch.aspx" target="_blank"> email search.</a>';

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $first_name = trim($form_state->getValue('first_name'));
        $last_name = trim($form_state->getValue('last_name'));
        $email_mail = trim($form_state->getValue('email_mail'));
        $requestParams = array('CollegeCode' => '062'); //put in the code for SeattleCentral
        if (!empty($email_mail)) {
            $requestParams['EmailAddress'] = $email_mail;
            $requestParams['UseWildcard'] = 0;
        } else {
            $requestParams['UseWildcard'] = 1;
        }
        if (!empty($first_name)) {
            $requestParams['FirstName'] = $first_name;
        }
        if (!empty($last_name)) {
            $requestParams['LastName'] = $last_name;
        }
        if (!empty($last_name)) {
            $requestParams['LastName'] = $last_name;
        }
        try {
            $client = new \SoapClient('http://seattlecolleges.edu/webservices/SCInformation.asmx?WSDL');
            $response = $client->getXMLDirectorySearch($requestParams);
            $data = $response->getXMLDirectorySearchResult->any;
            $employee_results = simplexml_load_string($data);

            if ($employee_results->Employee->nodata == TRUE) {
                $form_state->setValue('result', "No entries were found with the parameters. Sorry :(");
                $form_state->setRebuild();
            } else {
                $all_results = '';
                foreach ($employee_results->Employee as $employee) {
                    if ($employee->HideFromPublic == "false") {
                        $all_results = $this->parseData($employee, $all_results);
                    }
                }
                $form_state->setValue('result', $all_results);
                $form_state->setRebuild();

            }
        } catch (\SoapFault $fault) {
            $form_state->setValue('result', "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
            $form_state->setRebuild();
        };
    }

    private function parseData($employee, $all_results)
    {
        $response_data = "
      <div class=\"directory-result\">
      <h4>$employee->LastName , $employee->FirstName</h4>
          Email : $employee->WorkEmail <br/>";
        if (!empty($employee->DepartmentName)) {
            $response_data = $response_data . "Department : $employee->DepartmentName <br/>";
        }
        if (!empty($employee->WorkPhoneNumber)) {
            $response_data = $response_data . "WorkPhone : $employee->WorkPhoneNumber <br/>";
        }
        if (!empty($employee->MailStop)) {
            $response_data = $response_data . "MailStop : $employee->MailStop <br/>";
        }
        $response_data = $response_data . "</div>";
        return $all_results . $response_data;
    }
}
