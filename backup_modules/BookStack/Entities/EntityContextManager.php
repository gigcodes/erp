<?php

namespace Modules\BookStack\Entities;

use Illuminate\Session\Store;
use Modules\BookStack\Entities\Repos\EntityRepo;

class EntityContextManager
{
    protected $session;

    protected $entityRepo;

    protected $KEY_SHELF_CONTEXT_ID = 'context_bookshelf_id';

    /**
     * EntityContextManager constructor.
     */
    public function __construct(Store $session, EntityRepo $entityRepo)
    {
        $this->session = $session;
        $this->entityRepo = $entityRepo;
    }

    /**
     * Get the current bookshelf context for the given book.
     *
     * @return Bookshelf|null
     */
    public function getContextualShelfForBook(Book $book)
    {
        $contextBookshelfId = $this->session->get($this->KEY_SHELF_CONTEXT_ID, null);
        if (is_int($contextBookshelfId)) {
            /** @var Bookshelf $shelf */
            $shelf = $this->entityRepo->getById('bookshelf', $contextBookshelfId);

            if ($shelf && $shelf->contains($book)) {
                return $shelf;
            }
        }

        return null;
    }

    /**
     * Store the current contextual shelf ID.
     */
    public function setShelfContext(int $shelfId)
    {
        $this->session->put($this->KEY_SHELF_CONTEXT_ID, $shelfId);
    }

    /**
     * Clear the session stored shelf context id.
     */
    public function clearShelfContext()
    {
        $this->session->forget($this->KEY_SHELF_CONTEXT_ID);
    }
}
