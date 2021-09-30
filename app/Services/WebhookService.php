<?php

namespace App\Services;

use App\models\ClientTwilioNumbers;
use App\models\FormField;
use App\models\TelesalesSelfVerifyExpTime;
use Segment;
use App\User;
use App\models\Client;
use App\Traits\LeadTrait;
use App\models\WebhookResponse;

class WebhookService {

    use LeadTrait;

    public function __construct() {

    }

    /**
     * For API call to client's webhook when lead creates
     * @param $leadId, $url
     */
    public function leadCreateWebhookAPI($leadId, $url) {
        try {
            $leadArray = $this->getWebhookAPIRequest($leadId);

            $encodedReq = json_encode($leadArray);

            \Log::debug("leadCreateWebhookAPI : API request: " . $encodedReq);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla Chrome Safari');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedReq);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($encodedReq);
            // echo "<pre>"; print_r($headers); exit;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
	            echo 'Error:' . curl_error($ch);
	            \Log::debug("leadCreateWebhookAPI : CURL Error : " . curl_error($ch));
            }
	        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	        curl_close($ch);

            \Log::debug("leadCreateWebhookAPI : API response: " . $result);
            \Log::debug("leadCreateWebhookAPI : API response: " . $httpcode);


            if ($httpcode == 200) {
                \Log::info("leadCreateWebhookAPI : Lead create webhook api success with lead id: " . $leadId);
            } else {
                \Log::error("leadCreateWebhookAPI : Unable to call lead create Webhook api for lead id: " . $leadId);
            }

	        $webHookResponseLog = WebhookResponse::create([
		        'response' => $result,
		        'status_code' => $httpcode,
		        'lead_id' => $leadId
	        ]);

	        \Log::info("leadCreateWebhookAPI : Webhook database entry : " . $webHookResponseLog);

        } catch (\Exception $e) {
            \Log::error("leadCreateWebhookAPI : Lead create Webhook api error: " . $e->getMessage());
            \Log::error("leadCreateWebhookAPI : Lead create Webhook api error: " . $e);
        }
    }
}
