<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server;

use PhpMyAdmin\Url;
use PhpMyAdmin\Template;
use PhpMyAdmin\StorageEngine;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Controllers\AbstractController;

/**
 * Displays details about a given Storage Engine.
 */
final class ShowEngineController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(ResponseRenderer $response, Template $template, DatabaseInterface $dbi)
    {
        parent::__construct($response, $template);
        $this->dbi = $dbi;
    }

    /**
     * @psalm-param array{engine: string, page?: string} $params
     */
    public function __invoke(ServerRequest $request, array $params): void
    {
        global $errorUrl;

        $errorUrl = Url::getFromRoute('/');

        if ($this->dbi->isSuperUser()) {
            $this->dbi->selectDb('mysql');
        }

        $page = $params['page'] ?? '';

        $engine = [];
        if (StorageEngine::isValid($params['engine'])) {
            $storageEngine = StorageEngine::getEngine($params['engine']);
            $engine        = [
                'engine'     => $params['engine'],
                'title'      => $storageEngine->getTitle(),
                'help_page'  => $storageEngine->getMysqlHelpPage(),
                'comment'    => $storageEngine->getComment(),
                'info_pages' => $storageEngine->getInfoPages(),
                'support'    => $storageEngine->getSupportInformationMessage(),
                'variables'  => $storageEngine->getHtmlVariables(),
                'page'       => ! empty($page) ? $storageEngine->getPage($page) : '',
            ];
        }

        $this->render('server/engines/show', [
            'engine' => $engine,
            'page'   => $page,
        ]);
    }
}
