<?php

namespace App\Library\Google\DialogFlow;

use Google\Cloud\Dialogflow\V2\AgentsClient;
use Google\Cloud\Dialogflow\V2\EntityType;
use Google\Cloud\Dialogflow\V2\EntityType\Entity;
use Google\Cloud\Dialogflow\V2\EntityType\Kind;
use Google\Cloud\Dialogflow\V2\EntityTypesClient;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\IntentsClient;

class DialogFlowService
{
    private $googleAccount;
    private $credentials;

    public function __construct($googleAccount)
    {
        $this->googleAccount = $googleAccount;
        $this->credentials = ['credentials' => $googleAccount->service_file];
    }

    public function createIntent($parameters)
    {
        // Create Intents
        $intentClient = new IntentsClient($this->credentials);
        $parent = $intentClient->agentName($this->googleAccount->project_id);

        // Training Phrase
        $trainingPhrases = [];
        foreach ($parameters['questions'] as $question) {
            $part = (new Part())->setText($question);
            $trainingPhrase = (new TrainingPhrase())->setParts([$part]);
            $trainingPhrases[] = $trainingPhrase;
        }

        // Prepare message for intent
        $messages = [];
        foreach ($parameters['reply'] as $reply) {
            $text = (new Text())->setText([$reply]);
            $messages[] = (new Message())->setText($text);
        }

        // Prepare Intent
        $intent = (new Intent())->setDisplayName($parameters['name'])
            ->setTrainingPhrases($trainingPhrases)
            ->setMessages($messages);

        $response = $intentClient->createIntent($parent, $intent);
        $intentClient->close();
        return $response->getName();
    }

    public function deleteIntent($parameters)
    {
        $intentsClient = new IntentsClient($this->credentials);
        $projectId = $this->googleAccount->project_id;
        $intentId = $parameters['intent_id'];
        $intentName = $intentsClient->intentName($projectId, $intentId);
        $response = $intentsClient->deleteIntent($intentName);
        $intentsClient->close();
        return $response;
    }

    public function createEntity($entityTypeId, $entityValue, $synonyms = [])
    {
        // synonyms must be exactly [$entityValue] if the entityTypes'
        // kind is KIND_LIST
        if (!$synonyms) {
            $synonyms = [$entityValue];
        }

        $entityTypesClient = new EntityTypesClient($this->credentials);
        $parent = $entityTypesClient->entityTypeName($this->googleAccount->project_id, $entityTypeId);

        // prepare entity
        $entity = new Entity();
        $entity->setValue($entityValue);
        $entity->setSynonyms($synonyms);

        // create entity
        $response = $entityTypesClient->batchCreateEntities($parent, [$entity]);
        $entityTypesClient->close();
        return $response->getName();
    }

    public function deleteEntity($projectId, $entityTypeId, $entityValue)
    {
        $entityTypesClient = new EntityTypesClient($this->credentials);
        $parent = $entityTypesClient->entityTypeName($projectId, $entityTypeId);
        $response = $entityTypesClient->batchDeleteEntities($parent, [$entityValue]);
        $entityTypesClient->close();
        return $response;
    }

    public function createEntityType($displayName, $kind = Kind::KIND_MAP)
    {
        $entityTypesClient = new EntityTypesClient($this->credentials);

        // prepare entity type
        $parent = $entityTypesClient->agentName($this->googleAccount->project_id);
        $entityType = new EntityType();
        $entityType->setDisplayName(preg_replace('/\s+/', '_', $displayName));
        $entityType->setKind($kind);

        // create entity type
        $response = $entityTypesClient->createEntityType($parent, $entityType);
        $entityTypesClient->close();
        return $response->getName();
    }

    public function deleteEntityType($entityTypeId)
    {
        $entityTypesClient = new EntityTypesClient($this->credentials);
        $parent = $entityTypesClient->entityTypeName($this->googleAccount->project_id, $entityTypeId);
        $response = $entityTypesClient->deleteEntityType($parent);
        $entityTypesClient->close();
        return $response;
    }

    public function trainAgent()
    {
        $agentsClient = new AgentsClient($this->credentials);
//        _p($this->googleAccount);die;
        $parent = $agentsClient->projectName($this->googleAccount->project_id);
        $response = $agentsClient->trainAgent($parent);
        $agentsClient->close();
        return $response;
    }

    static public function deleteQuestion($question) {

    }
}
