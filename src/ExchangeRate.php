<?php

namespace Tecactus\Sunat;


use GuzzleHttp\Client;
use Tecactus\Sunat\Exception\InvalidDateException;
use Carbon\Carbon;

class ExchangeRate
{
    protected $client;
    protected $baseUri;
    protected $apiToken;

    public function __construct($apiToken)
    {
        // $this->baseUri = "https://tecactus.com/";
        $this->baseUri = "http://tecactus.app/";
        $this->apiToken = $apiToken;
        $this->client = new Client(['base_uri' => $this->baseUri, 'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->apiToken]]);
    }

    public function get($year, $month, $day = null, $forcePrevious = false, $asArray = false)
    {
        $this->validate($year, $month, $day);
        $response = $this->client->request('POST', 'api/sunat/exchange-rate', ['query' => $this->getQuery($year, $month, $day, $forcePrevious)]);
        return json_decode($response->getBody()->getContents(), $asArray);
    }

    protected function getQuery($year, $month, $day, $forcePrevious) {
        $query = [];
        $query['year'] = $year;
        $query['month'] = $month;
        if ($day) {
            $query['day'] = $day;
        }
        if ($forcePrevious) {
            $query['force_previous'] = $forcePrevious;
        }
        return $query;
    }

    protected function validate($year, $month, $day)
    {
        $currentDay = $this->getCurrentDate('day');
        $currentMonth = $this->getCurrentDate('month');
        $currentYear = $this->getCurrentDate('year');
        $lastDayOfMonth = $this->getCurrentDate('last_day_of_month');

        $day = is_null($day) ? $currentDay : $day;

        $date = Carbon::create($year, $month, $day)->startOfDay();

        if ($year > $currentYear) {
            throw new InvalidDateException("Year should be equal or lower to $currentYear.");
        } elseif (1 < $month && $month > 12) {
            throw new InvalidDateException("Month should be a value between 01 and 12.");
        } elseif (!(1 <= $day && $day <= $lastDayOfMonth)) {
            throw new InvalidDateException("Day should be a value between 01 and $lastDayOfMonth.");
        } elseif ($date->isFuture()) {
            throw new InvalidDateException("You cannot set a future date.");
        }
    }

    protected function getCurrentDate($value = null)
    {
        $currentDate = new Carbon();
        switch ($value) {
            case null:
                return $currentDate;
                break;
            case 'day':
                return $currentDate->day;
                break;
            case 'month':
                return $currentDate->month;
                break;
            case 'year':
                return $currentDate->year;
                break;
            case 'last_day_of_month':
                return $currentDate->endOfMonth()->day;
                break;
        }
    }
}