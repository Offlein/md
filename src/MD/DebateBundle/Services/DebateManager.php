<?php
namespace MD\DebateBundle\Services;
use \MD\DebateBundle\Entity\Debate;


class DebateManager
{
    protected $debate;

    public function __construct(Debate $debate = null)
    {
        $this->debate = $debate;

        if (!($this->debate->getName())) {
            throwException("fuck");
        }
    }

    // ...
}