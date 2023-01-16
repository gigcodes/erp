<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table\Structure;

use function __;
use PhpMyAdmin\Controllers\Table\AbstractController;
use PhpMyAdmin\Controllers\Table\StructureController;
use PhpMyAdmin\Database\CentralColumns;
use PhpMyAdmin\Message;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;

final class CentralColumnsRemoveController extends AbstractController
{
    /** @var CentralColumns */
    private $centralColumns;

    /** @var StructureController */
    private $structureController;

    public function __construct(
        ResponseRenderer $response,
        Template $template,
        string $db,
        string $table,
        CentralColumns $centralColumns,
        StructureController $structureController
    ) {
        parent::__construct($response, $template, $db, $table);
        $this->centralColumns = $centralColumns;
        $this->structureController = $structureController;
    }

    public function __invoke(): void
    {
        global $db, $message;

        $selected = $_POST['selected_fld'] ?? [];

        if (empty($selected)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No column selected.'));

            return;
        }

        $centralColsError = $this->centralColumns->deleteColumnsFromList($db, $selected, false);

        if ($centralColsError instanceof Message) {
            $message = $centralColsError;
        }

        if (empty($message)) {
            $message = Message::success();
        }

        ($this->structureController)();
    }
}
