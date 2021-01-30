<?php
namespace App\Forms;

use App\Globals\Treatment;

class Form {
    /**
     * @var string
     */
    private $error;

    public function createField($fields, $method, $url, $values = null) {
        $form = "<form method='$method' action='$url' class='d-flex flex-column align-items-center justify-content-center'>";
        foreach($fields as $field) {
            if(!empty($values)) {
                $value = $this->setField($values[$field['attr']['name']]);
            } else {
                $value = '';
            }
            $list = $this->listItems($field['attr']);
            if($field['attr']['type'] == 'textarea') {
                $form .= '<textarea '. $list . ' style="width: 100%" class="form-control m-2"></textarea>';
                continue;
            }
            $form .= '<input class="form-control m-2" '. $list .' value="'.$value.'">';
        }
        $form .= $this->createSubmit();
        $form .= "<form>";
        return $form;
    }

    public function listItems($attributes) {
        $attrs = '';
        foreach($attributes as $key => $attribute) {
            $attrs .= "$key='$attribute'";
        }
        return $attrs;
    }

    public function setField($field) {
        if(isset($field) && !empty($field)) {
            return $field;
        } else {
            return '';
        }
    }

    public function createSubmit() {
        return "<button class='btn btn-outline-info align-self-end' type='submit' value='send'>Envoyer</button>";
    }

    public function isValid($formEntity) {
        $treatment = new Treatment();
        // Récuperer la variable superglobale dans les params
        $fields = $formEntity::FIELDS;
        foreach($fields as $field) {
            if(isset($field['required']) && $field['required'] === true) {
                $value = $treatment->getPost($field['attr']['name']);
                if(isset($value) && !empty($value)) {
                    continue;
                } else {
                    $this->error = 'Le champs ' . $field['translate'] . ' est vide !';
                    return false;
                }
            }
        }

        return true;
    }

    public function isSubmitted($formEntity) {
        $treatment = new Treatment();
        $fields = $formEntity::FIELDS;
        foreach($fields as $field) {
            $value = $treatment->getPost($field['attr']['name']);
            if(isset($value)) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    public function getError() {
        return $this->error;
    }

}