<?php

/**
 * @author Daniele Agrelli <daniele.agrelli@gmail.com> on 02/07/18 09:33.
 * @copyright Copyright © Daniele Agrelli 2018
 *
 */

namespace lib;

/**
 * Class QaplaAPI
 * <br>
 * QaplaAPI is an easy way to call Qapla' API Web Service and get your results.
 * <br>
 * An example below:<br>
 *
 * $api = new QaplaAPI([
 *      'apiKey'=>'my secret api key' //REQUIRED!
 * ]);
 * <br>
 *
 * $api->getShipping('orderReference','100004399');
 *
 * @package lib
 *
 *
 */
class QaplaApiRest
{
    CONST URL = 'https://api.qapla.it/1.1/';

    CONST VERSION = 1.0;

    protected $apiKey;

    /**
     *
     *
     * @param array $params The key 'apiKey'=>'' MUST BE set
     */
    function __construct($params = [])
    {
        if (key_exists('apiKey', $params)):
            $this->apiKey = $params['apiKey'];
        else:
            die("You MUST insert an api key!!!");
        endif;
    }

    private function getRequestResults($action, $params, $method = 'GET')
    {
        $opts = array(
            'http' => array(
                'method' => $method,
                'header' => "Content-Type: application/json\r\n"
            )
        );

        $context = stream_context_create($opts);

        $params['apiKey'] = $this->apiKey;

        $result = json_decode(file_get_contents($this::URL . $action . '/?' . http_build_query($params), false, $context),true);

        return $result[$action];
    }

    /**
     * Get shipment status by tracking number or order reference
     * @param String $type The type of tracking number, only can be either "trackingNumber" or "orderReference"
     * @param String $tracking The tracking number or order reference number
     * @param String $lang The language of the results. Default to 'ita' (Italian language)
     * @return mixed The JSON result request
     */
    public function getShipping($type, $tracking, $lang = 'ita')
    {
        $params = [];

        switch ($type) {
            case 'trackingNumber':
                $params['trackingNumber'] = $tracking;
                break;
            case 'orderReference':
                $params['orderReference'] = $tracking;
                break;
            default:
                die('$type MUST BE \'trackingNumber\' or \'orderReference\' ');
        }

        $params['lang'] = $lang;
        return $this->getRequestResults('getTrack', $params);

    }

    /**
     * Get Shippings by date or day
     * @param string $dateType The type of date. Only "dateFrom" or "days"
     * @param mixed $value if $dateType = 'dateFrom' $value = YYYY-MM-DD HH:MM:SS , if is "days" $value = number of day from today
     * @return mixed The JSON result request
     */
    public function getShippingMultiple($dateType = 'dateFrom', $value)
    {
        $params = [];

        switch ($dateType) {
            case 'dateFrom':
                $params['dateFrom'] = $value;
                break;
            case 'days':
                $params['days'] = $value;
                break;
            default:
                die('$dateType MUST BE \'dateFrom\' or \'days\' ');
        }
        return $this->getRequestResults('getTracks', $params);
    }

    /**
     * Push one shipping in the Qapla' system
     * @param String $trackingNumber The number of tracking
     * @param String $courier The name of courier
     * @param String $shipDate The shipping date in ISO format (YYYY-MM-DD)
     * @param array $attributes You can set another shipping attributes via array( Possibile attributes are on https://api.qapla.it/#pushTrack
     * @return mixed The JSON result request
     */
    public function pushShipping($trackingNumber, $courier, $shipDate, $attributes = null)
    {
        $params = [];

        $shipping = [
            'trackingNumber' => $trackingNumber,
            'courier' => $courier,
            'shipDate' => $shipDate
        ];

        if ($attributes !== null):
            $shipping = array_merge($shipping, $attributes);
        endif;

        $params['pushTrack'] = $shipping;

        return $this->getRequestResults('pushTrack', $params, 'POST');
    }

    /**
     * Push one or more shippings in the Qapla' system
     * Ex:
     *
     * $attributes = [
     *      [
     *          "trackingNumber" => "123987299",
     *          "courier" => "DHL",
     *          "shipDate" => "2014-08-01"
     *      ], //minimun required fields example
     *      [
     *          "trackingNumber" => "1Z0V5V416840696736",
     *          "courier" => "UPS",
     *          "shipDate" => "2014-08-02",
     *          "reference" => "ord. # 1674",
     *          "orderDate" => "2014-07-30",
     *          "name" => "Pepito Sbazzeguti",
     *          . . .
     *      ]
     *
     * ]
     *
     *
     * @param array $attributes Possibiles attributes list of array are on https://api.qapla.it/#pushTrack
     * @return mixed The JSON result request
     */
    public function pushShippingMultiple($attributes)
    {
        $params = [];
        $params['pushTrack'] = $attributes;

        return $this->getRequestResults('pushTrack', $params, 'POST');
    }

    /**
     * Delete a shipment via trackingNumber
     * @param String $trackingNumber The tracking number of shipment to delete
     * @return mixed The JSON result request
     */
    public function deleteShipping($trackingNumber)
    {
        $params = [];
        $params['trackingNumber'] = $trackingNumber;

        return $this->getRequestResults('deleteTrack', $params, 'POST');
    }

    /**
     * Push one order into Qapla' system for more information see https://api.qapla.it/#pushOrder
     * @param $id
     * @param $status
     * @param $createdAt
     * @param $updatedAt
     * @param $customerName
     * @param $customerAddress
     * @param $customerCity
     * @param $customerState
     * @param $customerZip
     * @param $paymentType
     * @param null $attributes
     * @return mixed The JSON result request
     */
    public function pushOrder(
        $id,
        $status,
        $createdAt,
        $updatedAt,
        $customerName,
        $customerAddress,
        $customerCity,
        $customerState,
        $customerZip,
        $paymentType,
        $attributes = null
    ) {
        $params = [];

        $order = [
            'id' => $id,
            'status' => $status,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'customerName' => $customerName,
            'customerAddress' => $customerAddress,
            'customerCity' => $customerCity,
            'customerState' => $customerState,
            'customerZip' => $customerZip,
            'paymentType' => $paymentType,
        ];

        if ($attributes !== null):
            $order = array_merge($order, $attributes);
        endif;

        $params['pushOrder'] = $order;

        return $this->getRequestResults('pushOrder', $params, 'POST');
    }

    /**
     * Push one or more order in the Qapla' system
     * Ex:
     *
     * $attributes = [
     *      [
     *          'id'=>"2000994",
     *          'status'=>"processing",
     *          'createdAt'=>"2017-06-16 11:18:00",
     *          'updatedAt'=>"2017-06-16 16:09:20",
     *          'customerName'=>"Luca Cassia",
     *          'customerAddress'=>"Via Aieiez, 99",
     *          'customerCity'=>"Vedano Olona",
     *          'customerState'=>"VA",
     *          'customerZip'=>"21040",
     *          'paymentType'=>"ccash",
     *
     *      ], //minimun required fields example
     *      [
     *          'id'=>"2000992995",
     *          'status'=>"processing",
     *          'createdAt'=>"2017-06-16 11:18:00",
     *          'updatedAt'=>"2017-06-16 16:09:20",
     *          'customerName'=>"Roberto Fumarola",
     *          'customerAddress'=>"Via Icelord, 22",
     *          'customerCity'=>"Crispiano",
     *          'customerState'=>"TA",
     *          'customerZip'=>"74012",
     *          'paymentType'=>"creditcard",
     *          "amount"=> "EUR 110.05",
     *          "rows"=>[
     *              [
     *                  "sku"=> "B00TZMVRR4",
     *                  "name"=> "YoyoFactory Protostar Yo-Yo - Blu",
     *                  "qty"=> 1,
     *                  "price"=> "20.10",
     *                  "total"=> "20.10"
     *              ],
     *              [
     *                  "sku"=> "OXY4",
     *                  "name"=> "OXYGENE Oxy 4",
     *                  "qty"=> 1,
     *                  "price"=> "79.80",
     *                  "total"=> "79.80"
     *              ]
     *          ],
     *          . . .
     *      ],
     *
     * ]
     *
     *
     * @param array $attributes Possibiles attributes list of array are on https://api.qapla.it/#pushOrder
     * @return mixed The JSON result request
     */
    public function pushOrderMultiple($attributes)
    {
        $params = [];
        $params['pushOrder'] = $attributes;

        return $this->getRequestResults('pushOrder', $params, 'POST');
    }

    /**
     * Get Orders by date or day
     * @param string $dateType The type of date. Only "dateFrom" or "days"
     * @param mixed $value if $dateType = 'dateFrom' $value = YYYY-MM-DD HH:MM:SS , if is "days" $value = number of day from today
     * @return mixed The JSON result request
     */
    public function getOrdersMultiple($dateType = 'dateFrom', $value)
    {
        $params = [];

        switch ($dateType) {
            case 'dateFrom':
                $params['dateFrom'] = $value;
                break;
            case 'days':
                $params['days'] = $value;
                break;
            default:
                die('$dateType MUST BE \'dateFrom\' or \'days\' ');
        }
        return $this->getRequestResults('getOrders', $params);
    }

    /**
     * Get the list of all current couriers
     * @param string $country ISO-2 country code
     * @return mixed The JSON result request
     */
    public function getCouriers($country)
    {
        $params = [];
        $params['country'] = $country;

        return $this->getRequestResults('getCouriers', $params);
    }

    /**
     * Get the number of current account premium credits
     * @return mixed The JSON result request
     */
    public function getCredits()
    {
        $params = [];

        return $this->getRequestResults('getCredits', $params);
    }
}