<?php

namespace App\Library\Google\DialogFlow;

use App\Models\GoogleDialogAccountMails;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Service\Dialogflow;
use Google\Service\Oauth2;
use Google_Service_Oauth2;

class DialogFlowService
{

    private $scopes = [
        Dialogflow::CLOUD_PLATFORM,
        Dialogflow::DIALOGFLOW,
        Oauth2::USERINFO_PROFILE
    ];
    private $googleClient;
    private $googleAccount;

    public function __construct($googleAccount)
    {
        $this->googleAccount = $googleAccount;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->getClient()->createAuthUrl();
    }

    public function getClient(): \Google_Client
    {
        $this->googleClient = new \Google_Client();
        $this->googleClient->setClientId($this->googleAccount->google_client_id);
        $this->googleClient->setClientSecret($this->googleAccount->google_client_secret);
        $this->googleClient->setRedirectUri(route('google-chatbot-account.connect.login'));
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setScopes($this->scopes);
        return $this->googleClient;
    }

    public function getAccessToken($request)
    {
        $access_token = $this->getClient()->authenticate($request['code']);
        $oauth2 = new Google_Service_Oauth2($this->googleClient);
        $accountInfo = $oauth2->userinfo->get();

        $mail_acc = new GoogleDialogAccountMails();
        GoogleDialogAccountMails::where('google_account', $accountInfo->name)->delete();
        $mail_acc->google_account = $accountInfo->name;
        $mail_acc->google_dialog_account_id = $this->googleAccount->id;
        $mail_acc->google_client_access_token = $access_token['access_token'];
        if (!empty($access_token['refresh_token'])) {
            $mail_acc->google_client_refresh_token = $access_token['refresh_token'];
        }
        $mail_acc->expires_in = $access_token['expires_in'];
        $mail_acc->save();
    }

    public function createIntent()
    {
        // Create Intents
        $intentClient = new IntentsClient();
        $parent = $intentClient->agentName(123456);

        // Training Phrase
        $trainingPhrases = [];
        $part = (new Part())->setText('HEY');
        $trainingPhrase = (new TrainingPhrase())->setParts([$part]);
        $trainingPhrases[] = $trainingPhrase;

        // Prepare message for intent
        $text = (new Text())->setText(['Hello']);
        $message = (new Message())->setText($text);

        // Prepare Intent
        $intent = (new Intent())->setDisplayName('New Test Intent')
            ->setTrainingPhrases($trainingPhrases)
            ->setMessages([$message]);

        $response = $intentClient->createIntent($parent, $intent);
        $intentClient->close();
    }

    public function deleteIntent()
    {
        $intentsClient = new IntentsClient();
        $projectId = 123;
        $intentId = 345;
        $intentName = $intentsClient->intentName($projectId, $intentId);

        $intentsClient->deleteIntent($intentName);

        $intentsClient->close();
    }

    public function intentList($projectId)
    {
        $intentsClient = new IntentsClient();
        $parent = $intentsClient->agentName($projectId);
        $intents = $intentsClient->listIntents($parent);

        foreach ($intents->iterateAllElements() as $intent) {
            // print relevant info
            print(str_repeat('=', 20) . PHP_EOL);
            printf('Intent name: %s' . PHP_EOL, $intent->getName());
            printf('Intent display name: %s' . PHP_EOL, $intent->getDisplayName());
            printf('Action: %s' . PHP_EOL, $intent->getAction());
            printf('Root followup intent: %s' . PHP_EOL,
                $intent->getRootFollowupIntentName());
            printf('Parent followup intent: %s' . PHP_EOL,
                $intent->getParentFollowupIntentName());
            print(PHP_EOL);

            print('Input contexts: ' . PHP_EOL);
            foreach ($intent->getInputContextNames() as $inputContextName) {
                printf("\t Name: %s" . PHP_EOL, $inputContextName);
            }

            print('Output contexts: ' . PHP_EOL);
            foreach ($intent->getOutputContexts() as $outputContext) {
                printf("\t Name: %s" . PHP_EOL, $outputContext->getName());
            }
        }
        $intentsClient->close();
    }

    public function createContext($projectId, $contextId, $sessionId, $lifespan = 1)
    {
        $contextsClient = new ContextsClient();

        // prepare context
        $parent = $contextsClient->sessionName($projectId, $sessionId);
        $contextName = $contextsClient->contextName($projectId, $sessionId, $contextId);
        $context = new Context();
        $context->setName($contextName);
        $context->setLifespanCount($lifespan);

        // create context
        $response = $contextsClient->createContext($parent, $context);
        $contextsClient->close();
    }

    public function deleteContext($projectId, $contextId, $sessionId)
    {
        $contextsClient = new ContextsClient();

        $contextName = $contextsClient->contextName($projectId, $sessionId,
            $contextId);
        $contextsClient->deleteContext($contextName);
        $contextsClient->close();
    }

    public function contextList($projectId, $sessionId)
    {
        // get contexts
        $contextsClient = new ContextsClient();
        $parent = $contextsClient->sessionName($projectId, $sessionId);
        $contexts = $contextsClient->listContexts($parent);

        printf('Contexts for session %s' . PHP_EOL, $parent);
        foreach ($contexts->iterateAllElements() as $context) {
            // print relevant info
            printf('Context name: %s' . PHP_EOL, $context->getName());
            printf('Lifespan count: %d' . PHP_EOL, $context->getLifespanCount());
        }

        $contextsClient->close();
    }

    public function detectIntentTexts($projectId, $texts, $sessionId, $languageCode = 'en-US')
    {
        // new session
        $sessionsClient = new SessionsClient();
        $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
        printf('Session path: %s' . PHP_EOL, $session);

        // query for each string in array
        foreach ($texts as $text) {
            // create text input
            $textInput = new TextInput();
            $textInput->setText($text);
            $textInput->setLanguageCode($languageCode);

            // create query input
            $queryInput = new QueryInput();
            $queryInput->setText($textInput);

            // get response and relevant info
            $response = $sessionsClient->detectIntent($session, $queryInput);
            $queryResult = $response->getQueryResult();
            $queryText = $queryResult->getQueryText();
            $intent = $queryResult->getIntent();
            $displayName = $intent->getDisplayName();
            $confidence = $queryResult->getIntentDetectionConfidence();
            $fulfilmentText = $queryResult->getFulfillmentText();

            // output relevant info
            print(str_repeat('=', 20) . PHP_EOL);
            printf('Query text: %s' . PHP_EOL, $queryText);
            printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName,
                $confidence);
            print(PHP_EOL);
            printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);
        }

        $sessionsClient->close();
    }

    public function createEntity($projectId, $entityTypeId, $entityValue, $synonyms = [])
    {
        // synonyms must be exactly [$entityValue] if the entityTypes'
        // kind is KIND_LIST
        if (!$synonyms) {
            $synonyms = [$entityValue];
        }

        $entityTypesClient = new EntityTypesClient();
        $parent = $entityTypesClient->entityTypeName($projectId,
            $entityTypeId);

        // prepare entity
        $entity = new Entity();
        $entity->setValue($entityValue);
        $entity->setSynonyms($synonyms);

        // create entity
        $response = $entityTypesClient->batchCreateEntities($parent, [$entity]);
        printf('Entity created: %s' . PHP_EOL, $response->getName());

        $entityTypesClient->close();
    }

    public function deleteEntity($projectId, $entityTypeId, $entityValue)
    {
        $entityTypesClient = new EntityTypesClient();

        $parent = $entityTypesClient->entityTypeName($projectId,
            $entityTypeId);
        $entityTypesClient->batchDeleteEntities($parent, [$entityValue]);
        printf('Entity deleted: %s' . PHP_EOL, $entityValue);

        $entityTypesClient->close();
    }

    public function entityList($projectId, $entityTypeId)
    {
        $entityTypesClient = new EntityTypesClient();

        // prepare
        $parent = $entityTypesClient->entityTypeName($projectId,
            $entityTypeId);
        $entityType = $entityTypesClient->getEntityType($parent);

        // get entities
        $entities = $entityType->getEntities();
        foreach ($entities as $entity) {
            print(PHP_EOL);
            printf('Entity value: %s' . PHP_EOL, $entity->getValue());
            print('Synonyms: ');
            foreach ($entity->getSynonyms() as $synonym) {
                print($synonym . "\t");
            }
            print(PHP_EOL);
        }

        $entityTypesClient->close();
    }

    public function createEntityType($projectId, $displayName, $kind = Kind::KIND_MAP)
    {
        $entityTypesClient = new EntityTypesClient();

        // prepare entity type
        $parent = $entityTypesClient->agentName($projectId);
        $entityType = new EntityType();
        $entityType->setDisplayName($displayName);
        $entityType->setKind($kind);

        // create entity type
        $response = $entityTypesClient->createEntityType($parent, $entityType);
        printf('Entity type created: %s' . PHP_EOL, $response->getName());

        $entityTypesClient->close();
    }

    public function deleteEntityType($projectId, $entityTypeId)
    {
        $entityTypesClient = new EntityTypesClient();

        $parent = $entityTypesClient->entityTypeName($projectId,
            $entityTypeId);
        $entityTypesClient->deleteEntityType($parent);
        printf('Entity type deleted: %s' . PHP_EOL, $parent);

        $entityTypesClient->close();
    }

    public function entityTypeList($projectId)
    {
        // get entity types
        $entityTypesClient = new EntityTypesClient();
        $parent = $entityTypesClient->agentName($projectId);
        $entityTypes = $entityTypesClient->listEntityTypes($parent);

        foreach ($entityTypes->iterateAllElements() as $entityType) {
            // print relevant info
            printf('Entity type name: %s' . PHP_EOL, $entityType->getName());
            printf('Entity type display name: %s' . PHP_EOL, $entityType->getDisplayName());
            printf('Number of entities: %d' . PHP_EOL, count($entityType->getEntities()));
        }

        $entityTypesClient->close();
    }
}
