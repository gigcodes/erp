<?php

namespace Modules\BookStack\Entities;

use Illuminate\View\View;
use Illuminate\Support\Arr;

class BreadcrumbsViewComposer
{
    protected $entityContextManager;

    /**
     * BreadcrumbsViewComposer constructor.
     */
    public function __construct(EntityContextManager $entityContextManager)
    {
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Modify data when the view is composed.
     */
    public function compose(View $view)
    {
        $crumbs = $view->getData()['crumbs'];
        if (Arr::first($crumbs) instanceof Book) {
            $shelf = $this->entityContextManager->getContextualShelfForBook(Arr::first($crumbs));
            if ($shelf) {
                array_unshift($crumbs, $shelf);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}
