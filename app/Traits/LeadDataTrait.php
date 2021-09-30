<?php

namespace App\Traits;

use Log;
use App\models\Telesales;
use App\models\Clientsforms;
use App\models\Telesalesdata;

trait LeadDataTrait {

  /**
   * Retrieve Lead phone number value by it's form id
   * @param $leadId
   */
  public function getPhoneNumber($leadId) {
    \Log::info("Trait: " . $leadId);
    $lead = Telesales::find($leadId);

    if (empty($lead)) {
      return false;
    }

    \Log::info(print_r($lead, true));

    \Log::info("Form id:" . array_get($lead, 'form_id'));

    $form = Clientsforms::withTrashed()->find(array_get($lead, 'form_id'));

    \Log::info(print_r($form, true));

    if (empty($form)) {
      \Log::error("form empty !!");
      return false;
    }

    $field = $form->fields()->where('type', config()->get('constants.PHONE_NUMBER_TYPE'))->where('is_primary', 1)->first();

    if (empty($field)) {
      \Log::error("field empty !!");
      return false;
    }

    $fieldVal = Telesalesdata::where('field_id', array_get($field, 'id'))->where('telesale_id', array_get($lead, 'id'))->first();

    if (empty($fieldVal)) {
      \Log::error("field val empty !!");
      return false;
    }

    // return "9042973060";
    return array_get($fieldVal, 'meta_value');
  }
}
