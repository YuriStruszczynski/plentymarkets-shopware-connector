<?php

namespace PlentymarketsAdapter\ReadApi;

use PlentymarketsAdapter\Helper\LanguageHelper;

/**
 * Class Item
 */
class Item extends ApiAbstract
{
    /**
     * @param $productId
     *
     * @return array
     */
    public function findOne($productId)
    {
        $languageHelper = new LanguageHelper();

        return $this->client->request('GET', 'items/' . $productId, [
            'lang' => $languageHelper->getLanguagesQueryString(),
        ]);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $languageHelper = new LanguageHelper();

        return iterator_to_array($this->client->getIterator('items', [
            'lang' => $languageHelper->getLanguagesQueryString(),
        ]));
    }

    /**
     * @param $startTimestamp
     * @param $endTimestamp
     *
     * @return array
     */
    public function findChanged($startTimestamp, $endTimestamp)
    {
        $languageHelper = new LanguageHelper();

        return iterator_to_array($this->client->getIterator('items', [
            'lang' => $languageHelper->getLanguagesQueryString(),
            'updatedBetween' => $startTimestamp . ',' . $endTimestamp,
        ]));
    }
}