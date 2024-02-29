<?php

namespace Modules\BookStack\Entities;

/**
 * Class EntityProvider
 *
 * Provides access to the core entity models.
 * Wrapped up in this provider since they are often used together
 * so this is a neater alternative to injecting all in individually.
 */
class EntityProvider
{
    /**
     * @var Bookshelf
     */
    public $bookshelf;

    /**
     * @var Book
     */
    public $book;

    /**
     * @var Chapter
     */
    public $chapter;

    /**
     * @var Page
     */
    public $page;

    /**
     * @var PageRevision
     */
    public $pageRevision;

    /**
     * EntityProvider constructor.
     */
    public function __construct(
        Bookshelf $bookshelf,
        Book $book,
        Chapter $chapter,
        Page $page,
        PageRevision $pageRevision
    ) {
        $this->bookshelf    = $bookshelf;
        $this->book         = $book;
        $this->chapter      = $chapter;
        $this->page         = $page;
        $this->pageRevision = $pageRevision;
    }

    /**
     * Fetch all core entity types as an associated array
     * with their basic names as the keys.
     *
     * @return Entity[]
     */
    public function all()
    {
        return [
            'bookshelf' => $this->bookshelf,
            'book'      => $this->book,
            'chapter'   => $this->chapter,
            'page'      => $this->page,
        ];
    }

    /**
     * Get an entity instance by it's basic name.
     *
     * @return Entity
     */
    public function get(string $type)
    {
        $type = strtolower($type);

        return $this->all()[$type];
    }

    /**
     * Get the morph classes, as an array, for a single or multiple types.
     *
     * @param string|array $types
     *
     * @return array<string>
     */
    public function getMorphClasses($types)
    {
        if (is_string($types)) {
            $types = [$types];
        }

        $morphClasses = [];
        foreach ($types as $type) {
            $model          = $this->get($type);
            $morphClasses[] = $model->getMorphClass();
        }

        return $morphClasses;
    }
}
