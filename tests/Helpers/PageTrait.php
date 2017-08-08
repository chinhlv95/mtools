<?php

namespace Tests\Helpers;

trait PageTrait
{
    protected function isNotErrorPage()
    {
        return $this
            ->dontSee(trans('application_lang.404_page_not_found'))
            ->dontSee(trans('application_lang.500_system_error'));
    }

    protected function clickBySelector($selector)
    {
        $link = $this->crawler->filter($selector);

        if (! count($link)) {
            throw new InvalidArgumentException(
                "Could not find a link with selector [{$selector}]"
            );
        }

        $this->visit($link->link()->getUri());

        return $this;
    }
}
