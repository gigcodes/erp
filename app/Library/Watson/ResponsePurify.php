<?php

namespace App\Library\Watson;

/**
 * Watson reply condition match
 *
 *
 *
 */

class ResponsePurify
{

    const EXCLUDED_REPLY = [
        "Can you reword your statement? I'm not understanding.",
        "I didn't understand. You can try rephrasing.",
        "I didn't get your meaning.",
    ];

    public $images;
    public $response;
    public $customer;
    public $entities = [];
    public $intents  = [];
    public $context;

    public function __construct($response, $customer = null)
    {
    	$this->response = $response->output;
        $this->context  = isset($response->context) ? $response->context : null;

        $this->customer = $customer;

        if ($this->isValid()) {
            $this->settleResponse();
        }
    }

    private function settleResponse()
    {
        $result = $this->response;

        // settle intetnts
        if (isset($result->intents)) {
            foreach ($result->intents as $intents) {
                $this->intents[$intents->intent] = $intents;
            }
        }

        // assign entities
        if (isset($result->entities)) {
            foreach ($result->entities as $entities) {
                $this->entities[$entities->entity] = $entities;
            }
        }

    }

    /**
     * Match action and assign to the method so it can be go ahead
     *
     */

    public function assignAction()
    {
        $medias = $this->isNeedToSendProductImages();
        $text   = $this->getReplyText();
        // if match action then assign
        if (!empty($medias)) {
            return ["action" => "send_product_images", "reply_text" => $text, "response" => $this->response, "medias" => $medias];
        }

        if (!empty($text)) {
            return ["action" => "send_text_only", "reply_text" => $text];
        }

        return null;
    }

    /**
     * Check response is valid or not
     * @return  false
     *
     */

    public function isValid()
    {
        return (isset($this->response) && isset($this->response->generic)) ? true : false;
    }

    public function getReplyText()
    {
        $textMessage = reset($this->response->generic);

        if (isset($textMessage->text)) {
            if (!in_array($textMessage->text, self::EXCLUDED_REPLY)) {
                return $textMessage->text;
            }
        }
    }

    private function isNeedToSendProductImages()
    {
        $entity      = "product";
        $intentsList = ["Customer_Care_Products_Offered", "Customer_Brand_Enquiry"];
        $gender      = null;
        if (isset($this->customer) && isset($this->customer->gender)) {
            $gender = ($this->customer->gender == "male") ? 3 : 2;
        }
        // first match with scenerios
        foreach ($intentsList as $intents) {
            if (
                in_array($entity, array_keys($this->entities))
                && in_array($intents, array_keys($this->intents))
            ) {
                $attributes = isset($this->entities[$entity]) ? $this->entities[$entity] : null;
                $sendImages = new Action\SendProductImages($attributes, $params = [
                    "gender" => $gender,
                ]);
                if ($sendImages->isOptionMatched()) {
                    return $sendImages->getResults();
                }

            }
        }

        // now check with context scenrio of this again
        $context = $this->context;
        if(!empty($context)) {
        	if(!empty($context->skills)) {
        		$attributes = "";
        		foreach($context->skills as $skills) {
        			if(!empty($skills->user_defined->brand_name)) {
        				$attributes .= $skills->user_defined->brand_name;
        				if(!empty($skills->user_defined->category_name)) {
	        				$attributes .= $skills->user_defined->category_name;
	        				// if brand and category both setup then 
	        				$obect = new \stdClass;
	        				$obect->attributes = $attributes;
	        				$sendImages = new Action\SendProductImages($attributes, $params = [
			                    "gender" => $gender,
			                ]);
			                if ($sendImages->isOptionMatched()) {
			                    return $sendImages->getResults();
			                }
	        			}
        			}
        		}
        	}
        }


        return false;
    }

    public function getEntities()
    {
        return false;
    }

}
